if(typeof sipCredentials === "undefined") {
    throw new Error("Sorry! No SIP credentials found");
}

// Set getUserMedia for all browsers
navigator.getUserMedia = ( navigator.getUserMedia ||
                            navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia ||
                            navigator.msGetUserMedia
                        );

if(navigator.getUserMedia !== undefined) {
        
    // Check if there have microphone connected
    //var userMedia = navigator.getUserMedia() || navigator.mediaDevices.getUserMedia();
    navigator.getUserMedia(
        {audio: true},
        function(){

            // enable call button
            $("#callButton").prop("disabled", false);

        }, 
        function() {
            // error callback, no microphone
            $("#status").html("<div style='font-size: 14px;' class='alert alert-danger'>No microphone found! Please connect your mic and reload!</div>").show();

            // disable call button
            $("#callButton").prop("disabled", true);
            return;
        }
    );

} else {

    // Show error no user medai
    $("#status").html("<div style='font-size: 14px;' class='alert alert-danger'>Sorry! No media found. This module will not work.</div>").show();

    // disable call button
    $("#callButton").prop("disabled", true);

}



var remoteAudio = new window.Audio();
remoteAudio.autoplay = true;
remoteAudio.crossOrigin="anonymous";

var socket = new JsSIP.WebSocketInterface(sipCredentials.socket);
var configuration = {
    sockets  : [ socket ],
    'uri': sipCredentials.uri, 
    'password': sipCredentials.pass,
    'username': sipCredentials.user,
    'register': true
};


var callOptions = {
    mediaConstraints: {audio: true, video: false}
};


var caller = "";
var callerDisplay = "";
var hideNumbers = ['*78', '*79', '*76'];

var phone = new JsSIP.UA(configuration);

phone.start();
var session;

phone.on('newRTCSession', function(data){

    var newSession = data.session;
    
    if(session){ // if any existing session is running then terminate the new call
        
        /**
         * Add a call log if any call is coming while the agents are busy
         */
        addCallLog({
            caller: newSession.remote_identity.uri.user,
            direction: newSession.direction,
            duration: 0,
            status: "Missed"
        });

        newSession.terminate();
        return false;
        
    }

    session = newSession;
    var completeSession = function(){

        session = null;

        // Reset the mute and holde button
        $("#muteCall, #holdCall").show();
        $("#unholdCall, #unmuteCall").hide();

    };

    caller = session.remote_identity.uri.user;
    callerDisplay = session.remote_identity.display_name;

    // Show number in sms receipient
    $("#csSmsSendTo").val(caller);

    // Show number in feedback caller
    $("#feedbackCaller").val(caller);


    session.on('ended', function() {

        // Do not show/ Add the caller details for hidden numbers
        if(  jQuery.inArray(caller, hideNumbers) < 0 ) {
        
            addCallLog({
                caller: caller,
                direction: session.direction,
                duration: Math.floor( (session.end_time.getTime() - session.start_time.getTime()) / 1000),
                status: "Answered"
            });

            // Pause Ring
            BMS.fn.pause();


            // Stop Timer
            BMS.fn.stopTimer(window.timer);

            $("#status").html(`${caller} is disconnected`).show();


            $("#timer").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(500).fadeIn(500);

            // disable the number search and hangButton
            $("#hangButton").prop("disabled", true);

            // Show the feedback tab
            $('.nav-tabs a[href="#tab_feedback"]').tab('show');

            // Stop the phone
            phone.stop();

        }

        // Complete the session
        completeSession();
        
    });


    session.on('failed', function(e) {
        
        // Show the call dialer after session end
        $(".call-dialer").show();

        // Hide call Received
        $("#callReceiver").hide();

        // Pause Ring
        BMS.fn.pause();

        // Show the call button
        $("#callButton").show();

        // Hide the end button
        $("#hangButton").hide();

        // Enable the number search and hangButton
        $(".numberSearch, #hangButton").prop("disabled", false);
        $(".number-dialer-div .input-group-addon").removeClass("disable-number-dialer-div");
        

        if(session.direction === 'incoming' ) {


            if( e.originator === "local" ) {

                $("#status").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(250).fadeIn(250, function() {
                    
                    $("#status").html(`Call rejected!`).show();

                    addCallLog({
                        caller: caller,
                        direction: 'incoming',
                        duration: 0,
                        status: "Rejected"
                    });

                });

            } else {

                $("#status").html(`Missed call from ${callerDisplay}!`).show();
                addCallLog({
                    caller: caller,
                    direction: 'incoming',
                    duration: 0,
                    status: "Missed"
                });

            }

            
        } else {

            if( e.originator === "local" ) {

                $("#status").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(250).fadeIn(250, function() {
                    
                    $("#status").html(`Dial a number!`).show();

                    addCallLog({
                        caller: caller,
                        direction: 'outgoing',
                        duration: 0,
                        status: "Unreachable"
                    });

                });

            } else {

                $("#status").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(250).fadeIn(250, function() {


                    if( e.cause === "SIP Failure Code" ) {
                        
                        $("#status").html(`The number is not answered!`).show();

                        addCallLog({
                            caller: caller,
                            direction: 'outgoing',
                            duration: 0,
                            status: "Not Answered"
                        });

                    } else {
                        
                        $("#status").html(`The number is busy!`).show();
                        addCallLog({
                            caller: caller,
                            direction: 'outgoing',
                            duration: 0,
                            status: "Busy"
                        });

                    }

                    // Show the feedback tab for writing feedback if the call is not received or terminate by the remote caller
                    $('.nav-tabs a[href="#tab_feedback"]').tab('show');

                    // Stop the phone
                    // phone.stop();


                });

            }

        }
        
        completeSession();
        
    });

    session.on("hold", function(e) {

        if(e.originator === "remote") {
            
            $("#status").html(`${caller} is hold you.`).show();

        } else {
            
            $("#status").html(`${caller} is on hold.`).show();

        }

    });

    session.on("muted", function(e) {
        
        $("#status").html(`Call is muted.`).show();

    });

    session.on("unmuted", function(e) {
        
        $("#status").html(`${caller} is connected.`).show();

    });

    session.on("unhold", function() {
    
        $("#status").html(`${caller} is connected.`).show();

    });

    session.on('accepted', function(){

        // Do not show the caller details for hidden numbers
        if(  jQuery.inArray(caller, hideNumbers) < 0 ) {

            // Show timer
            $("#timer").show();
            window.timer = BMS.fn.startTimer();

            $("#status").html(`${caller} is connected.`).show();
            
        }

        
    });

    session.on('confirmed',function(){
        var localStream = session.connection.getLocalStreams()[0];
        var dtmfSender = session.connection.createDTMFSender(localStream.getAudioTracks()[0]);
        session.sendDTMF = function(tone){
            dtmfSender.insertDTMF(tone);
        };
        
    });


    session.on('peerconnection', (e) => {

        let logError = '';
        const peerconnection = e.peerconnection;

        peerconnection.onaddstream = function (e) {

            // set remote audio stream (to listen to remote audio)
            // remoteAudio is <audio> element on pag
            remoteAudio.srcObject = e.stream;
            remoteAudio.play();

        };

        var remoteStream = new MediaStream();

        peerconnection.getReceivers().forEach(function (receiver) {
            remoteStream.addTrack(receiver.track);

        });


    });
    
    if(session.direction === 'incoming'){


        BMS.fn.play("incoming_call", true);

        $("#status").html(`${callerDisplay} is calling...`).show();

        // Show the call Receiver
        $("#callReceiver").show();

        // Hide timer
        $("#timer").hide();

        // Hide Call dialer
        $(".call-dialer").hide();

        // Show the caller details
        showCallerDetails(caller);


    } else {

        session.connection.addEventListener('addstream', function(e){
            BMS.fn.pause();
            remoteAudio.srcObject = e.stream;
        });     

    }

});


// Call to the inputed number
$(document).on("click", "#callButton", function() {

    var number = $(".numberSearch").val();
    
    dial(number);

});


// Hangup the Call
$(document).on("click", "#hangButton", function() {

    session.terminate();

});

// terminate the session while closing the browser
$(window).on("beforeunload", function() {
   
    session.terminate();

});

// Mute the Call
$(document).on("click", "#muteCall", function() {

    if(typeof session === "undefined") {
        return BMS.fn.alertError("Sorry! There is no active call.");
    }

    session.mute();

    // hide mutecall button and show unmute call
    $("#muteCall, #unmuteCall").toggle();

});

// Unmute the Call
$(document).on("click", "#unmuteCall", function() {

    if(typeof session === "undefined") {
        return BMS.fn.alertError("Sorry! There is no active call.");
    }

    session.unmute();

    // show mutecall button and hide unmute call
    $("#muteCall, #unmuteCall").toggle();

});

$(document).on("click", "#holdCall", function() {

    if(typeof session === "undefined") {
        return BMS.fn.alertError("Sorry! There is no active call.");
    }

    session.hold();
    // hide holdCall button and show unholdCall call
    $("#holdCall, #unholdCall").toggle();

});


$(document).on("click", "#unholdCall", function() {

    if(typeof session === "undefined") {
        return BMS.fn.alertError("Sorry! There is no active call.");
    }

    session.unhold();
    // show hidecall button and hide unholdCall call
    $("#holdCall, #unholdCall").toggle();

});

$(document).on("click", "#transferCall", function() {

    if(typeof session === "undefined") {
        return BMS.fn.alertError("Sorry! There is no active call to transfer.");
    }

    /** Generate options for sweet alert */
    var generateOptions = {};
    $.each(extentionNumbers, function(key, item) {
        generateOptions[item.extention] = item.name;
    });

    // Transfer the Call
    Swal.fire({
        title: "Please select the extension",
        input: "select",
        inputOptions: generateOptions,
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-arrow-up"></i> Transfer Call',
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "Destination number..."
    }).then((results) => {

        if(results.value !== undefined) {

            // Transfer and terminate session here
            session.refer(results.value);
            session.terminate();

        }

    });

});


/** Enable Do not disturb mode, by rejecting calls */
$(document).on("click", "#enableDND", function() {

    // Enable Donotdisturb in PBX side (Asterisk)
    phone.call("*78", callOptions);

    // Save the state in local
    localStorage.setItem("dnd", "enable");

    // hide enableDND button and show disableDND
    $("#enableDND, #disableDND, .dndAlert").toggle();

});


$(document).on("click", "#disableDND", function() {


    // Disable Donotdisturb in PBX side (Asterisk)
    phone.call("*79", callOptions);

    // Save the disable state in local
    localStorage.setItem("dnd", "disable");

    // hide disableDND button and show enableDND
    $("#enableDND, #disableDND, .dndAlert").toggle();

});


// Answer Incoming Call
$(document).on("click", "#callReceiveButton", function() {
    
    // Answer call
    session.answer(callOptions);

    // Show timer
    $("#timer").show();
    
    // Paush the ring
    BMS.fn.pause();

});

// Reject Incoming Call
$(document).on("click", "#IncomingCallHangButton", function() {
    
    // reject call
    session.terminate();

    // Paush the ring
    BMS.fn.pause();

});


$(document).on("click", "#csSendSMS", function() {

    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=sendSMS",
        type: "post",
        data: {
            "number": $("#csSmsSendTo").val(),
            "text": $("#csSmsText").val()
        }, 
        success: function(data, status) {

            if(status = "success" && data === "1") {

                // Success Altert
                BMS.fn.alertSuccess("SMS successfully sent.", false);

                // Make empty the sms box
                $("#csSmsText").val("");


            } else {
                BMS.fn.alertError("SMS faild to send.");
            }

        }
        
    });

});


$(document).on("click", ".addToSMSBox tr", function() {
    
    var representativeInfo = "";
    $("td", this).slice(3,6).each(function() {

        representativeInfo += $(this).text() + "\n";

    });
    

    $("#csSmsText").val( function() {
        return this.value + representativeInfo;
    });
    
    
});

$(document).on("click", "#addNewCase", function() {

    var url = full_website_address + `/xhr/?module=customer-support&page=newCase&num=`;
    
    $("#modalDefaultMdm").modal('show').find('.modal-content').load( url + encodeURI(caller) );

});


function addCallLog({caller="", direction="", duration="", status=""}="") {

    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=addCallLog",
        type: "post",
        data:{
            caller: caller,
            direction: direction,
            duration: duration,
            status: status,
            feedback: $("#callFeedback").val(),
            reviewer: $("#feedbackReviewer").val()
        },
        success: function(data, status) {
        }

    });

}


$(document).on("click", '#saveFeedback', function() {


    var callReason = $("#callReason").find(':selected').val();

    if( callReason === "" ) {

        BMS.fn.alertError("Please select call reason");

    } else {

        // Update the call feedback
        $.ajax({
            url: full_website_address + "/xhr/?module=customer-support&page=updateCallFeedback",
            type: "post",
            data: {
                caller: $("#feedbackCaller").val(),
                reason: callReason,
                feedback: $("#callFeedback").val(),
                specimenReceived: $("#specimenCopyReceived").val(),
                informative: $("#feedbackInformative").val(),
                saleOurProduct: $("#SaleOurProduct").val(),
                userOurProduct: $("#userOurProduct").val(),
                mrFeedback: $("#mrFeedback").val(),
                otherInformation: $("#otherInformation").val()
            },
            success: function(data, status) {
                
                if(status = "success" && data.includes('success') ) {

                    // Success Altert
                    BMS.fn.alertSuccess( $(data).text() , false);

                    // Make empty the boxes of feeback
                    $("#feedbackCaller").val("");
                    $("#callFeedback").val("");


                } else {

                    BMS.fn.alertError($(data).text());

                }


                // Hide all hidden tab
                $(".hidden_tab").hide();
                
                // Active the lead tab
                $('.nav-tabs a[href="#tab_messages"]').tab('show');


                // Enable the number search and hangButton
                $(".numberSearch, #hangButton").prop("disabled", false);
                $(".number-dialer-div .input-group-addon").removeClass("disable-number-dialer-div");

                // Reset and hide the timer
                $("#timer").html("00:00:00").hide();

                // Hide the status
                $("#status").hide();

                
                // Show the call button and hide the hang button
                $("#hangButton").hide();
                $("#callButton").show();

                // Show the call dailer
                $(".call-dialer").show();

                // hide the call reciver
                $("#callReceiver").hide();

            }

        });


        // Start the phone either the feedback is saved or not
        phone.start();

    }

});


// Insert quick feedback in feedback box
$(document).on("click", ".quickFeedback > ul > li:not(:last-child)", function() {
    
    $("#callFeedback").val( $("#callFeedback").val() + $(this).text() + "\n" );

});

// add new feedback
$(document).on("click", ".quickFeedback > ul > li:last-child", function() {
    
    Swal.fire({
        title: "Enter new feedback",
        input: "textarea",
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "Write feedback here"
    }).then((results) => {

        if(results.value !== undefined) {

             // Save feedback in database
            $.ajax({
                url: full_website_address + "/xhr/?module=customer-support&page=addNewNote",
                type: "post",
                data:{
                    note: results.value,
                    type: "feedback"
                }
            });

            // Add in the list
            $(".quickFeedback > ul > li:last-child").before( `<li style='cursor:pointer;'>${results.value}</li>` );

        }

    });

});

// Insert note on sms box
$(document).on("click", ".userNote > ul > li:not(:last-child)", function() {

    console.log("hi");
    
    var that = this;
    $("#csSmsText").val( function() {
        return this.value + $(that).text() + "\n";
    });

});

// add new note
$(document).on("click", ".userNote > ul > li:last-child", function() {
    
    Swal.fire({
        title: "Enter note here",
        input: "textarea",
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "Write note here"
    }).then((results) => {

        if(results.value !== undefined) {

             // Save feedback in database
            $.ajax({
                url: full_website_address + "/xhr/?module=customer-support&page=addNewNote",
                type: "post",
                data:{
                    note: results.value,
                    type: "note"
                }
            });

            // Add in the list
            $(".userNote > ul > li:last-child").before( `<li style='cursor:pointer;'>${results.value}</li>` );

        }

    });

});


// Get caller details by pressing enter
$(document).on('keypress', '.numberSearch', function(e){

    if(e.key === "Enter") {

        var number = $(this).val();

        if( number.length < 2 ) {
            alert("Please enter a valid number to continue");
        } else {
            showCallerDetails(number)
        }

    }

});


function dial(number) {
    

    if( phone.isConnected() ) {

        // Disable input filed after dial
        $(".numberSearch").prop("disabled", true);
        $(".number-dialer-div .input-group-addon").addClass("disable-number-dialer-div");
        
        // BMS.fn.play("outgoing_call", true);
        session = phone.call( number.trim() , callOptions);

        $("#status").html(`Call is being send to ${number}...`).show();

        // hide the call button and show the hang button
        $("#callButton, #hangButton").toggle();


        // Show the caller details
        showCallerDetails(caller);

        // Empty the feedback area and remove previous fired for savings
        $("#callFeedback, #feedbackReviewer").val("");
        $(document).off("change", "#callFeedback, #feedbackReviewer");


        // Hide the  suggestion number if it is shown
        $(".suggestion-numbers").html("").hide();


    } else {

        $("#status").html(`<div style="font-size: 16px;" class='alert alert-danger'>Sorry! The signaling server is disconnected.</div>`).show();

    }


}

function showCallerDetails(number) {


    // Show the caller details
    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=getCallerAllDetails",
        type: "post",
        data: {
            "caller": number
        },
        success: function(data, status) {
            
            if(status == "success") {

                var callerData = JSON.parse(data);


                // Notifiy only on incoming session
                if( session && session.direction === 'incoming' ) {

                    if(callerData.details.type !== undefined) {
                        BMS.fn.desktopNotify(`${callerData.details.name} (${number}) is calling...`, full_website_address +"/assets/images/call.png", `${callerData.details.type}, ${callerData.details.designation}`);
                    } else {
                        BMS.fn.desktopNotify(`${number} is calling...`, full_website_address +"/assets/images/call.png", "Click Here to open the dashboard");
                    }

                }


                if( Object.keys(callerData.details).length > 0) {

                    $(".callerInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html( callerData.details.name );
                    $(".callerInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html( callerData.details.type );
                    $(".callerInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html( callerData.details.designation );
                    $(".callerInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html( callerData.details.address);
                    $(".dataToCopy").html( `<table><tr><td>${callerData.details.name}, ${callerData.details.type} \n ${callerData.details.designation}</td>\
                                            <td>${number}</td>\
                                            <td>${callerData.details.address}</td></tr></table>`);


                    var actionButtons = `<a class="btn btn-primary" data-toggle="modal" href="<?php echo full_website_address(); ?>${callerData.details.editUri}${callerData.details.id}" data-target="#modalDefault">Edit ${callerData.details.type}</a>`;
                    if( callerData.details.has_sc == 1 ) {
                        actionButtons += `<a style="margin-left: 15px;" class="btn btn-primary" data-toggle="modal" href="<?php echo full_website_address(); ?>/xhr/?module=marketing&page=viewSpecimenProduct&id=${callerData.details.id}"  data-target="#modalDefault">View Specimen Copies</a>`;
                    }

                    actionButtons += `<button style="margin-left: 15px;" type="button" class="btn btn-info" 
                                        onclick="BMS.fn.copy('.dataToCopy')" 
                                    >
                                        Copy
                                    </button>`;

                    $(".callerInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html(actionButtons);


                } else {

                    $(".callerInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html("No data found in database");
                    $(".callerInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html("");

                }

                
                // Remove Previous Order history
                $(".callerOrderInfo > tbody").html('<tr><td colspan="4">Sorry! No order found.</td></tr>');

                // Show order History
                if(callerData.orderHistory.length > 0) {

                    var orderHtml = "";
                    callerData.orderHistory.forEach(item => {

                        orderHtml += `<tr>
                            <td>${item.date}</td>
                            <td> <a data-toggle='modal' data-target='#modalDefault' href='<?php echo full_website_address(); ?>/xhr/?module=reports&page=showInvoiceProducts&id=${item.id}'> ${item.reference} </a> </td>
                            <td>${item.total}</td>
                            <td>${item.payment_status}</td>
                            <td class="text-center">
                                <iledit data-val="${item.status}">${item.status}</iledit>
                                <pkey>${item.id}</pkey>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-flat btn-primary" data-toggle='modal' data-target='#modalDefault' href='<?php echo full_website_address(); ?>/xhr/?module=my-shop&page=editSaleNote&id=${item.id}'> Edit Note </a>
                                <a target="_blank" class="btn btn-xs btn-flat btn-primary" href='<?php echo full_website_address(); ?>/sales/pos/?edit=${item.id}'> Edit Sale</a>
                            </td>
                        </tr>`;

                    });


                    $(".callerOrderInfo > tbody").hide().html(orderHtml).show("fast");

                }


                // Remove previous Call History
                $(".call-history").html("<p>No call</p>");
                // show the Call count
                $(".call-count").html(`( ${ callerData.totalCallCount } )`)
                
                if( callerData.callHistory.length > 0 ) {
                    
                    var callHistory = "";
                    callerData.callHistory.forEach(item => {
                        callHistory += `<div class="call-item">
                                            <p style="font-weight: bold;" >Cause: <span style='color: green;'>${item.call_reason}</span>; 
                                            <span style="margin-left: 10px;"><i class="fa fa-clock-o"></i> ${ new Date(item.duration * 1000).toISOString().substr(14, 5) }</span> </p>
                                            <p>
                                                <span>Direction: <b>${item.call_direction}</b> </span>
                                                <span style="margin-left: 10px;">Status: <b>${item.call_status}</b> </span>
                                            </p>
                                            <p>${item.call_datetime}; By- <b>${item.agent_name}</b></p>
                                            <p class="bg-info" style="padding: 2px 5px;">${item.feedback}</p>

                                        </div>`;
                    });
                    
                    // Show the call history
                    $(".call-history").hide().html(callHistory).show('fast');

                    // If the call history is more then five then add a load more button
                    if( Number(callerData.totalCallCount) > 5 ) {
                        
                        $(".call-history").append(`<div class="text-center"><button type="button" class="btn btn-info loadCallHistory">Load More</button></div>`);

                    }
                    
                }


                // Remove previous Cases History
                $(".case-history").html("<p>No Cases</p>");
                    
                if( callerData.caseHistory.length > 0 ) {
                    
                    var caseHistory = "";
                    callerData.caseHistory.forEach(item => {
                        caseHistory += `<div class="call-item">
                                            <div class="call-item">
                                                <p style="font-weight: bold;"> <a target="_blank" href='<?php echo full_website_address(); ?>/customer-support/case-list/?case_id=${item.case_id}'>${item.case_title}</a> </p>
                                                <p><b>Status:</b> ${item.case_status}</p>
                                                <p>${item.case_datetime}; By- <b>${item.posted_by_name}</b></p>
                                            </div>
                                        </div>`;
                    });
                    
                    // Show the call history
                    $(".case-history").hide().html(caseHistory).show('fast');
                    
                }



                // Remove previous SMS History
                $(".case-history").html("<p>No Cases</p>");
                    
                if( callerData.smsHistory.length > 0 ) {
                    
                    var smsHistory = "";
                    callerData.smsHistory.forEach(item => {
                        smsHistory += `<div class="message-item">
                                            <p>${item.sms_text}</p>
                                            <p>${item.send_time}; By- <b>${item.agent_name}</b></p>
                                        </div>`;
                    });
                    
                    // Show the call history
                    $(".message-history").hide().html(smsHistory).show('fast');
                    
                }


            }
        }

    });


    // Show All tabs and activate leads tab
    $(".call-center-tabs > li").show();
    
    // Active the lead tab
    $('.nav-tabs a[href="#tab_leads"]').tab('show');

}


$(document).on("click", ".loadCallHistory", function() {


    // Ad loading icon
    $(".loadCallHistory").html("<i class='fa fa-spin fa-refresh'></i> Loading...");
    

    // Show the caller details
    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=getCallHistoryData",
        type: "post",
        data: {
            "caller": $(".numberSearch").val()
        },
        success: function(data, status) {
            
            if(status == "success") {

                var callHistory = JSON.parse(data);

                if( callHistory.length > 0 ) {
                    
                    var callHistoryDisplay = "";
                    callHistory.forEach(item => {
                        callHistoryDisplay += `<div class="call-item">
                                            <p style="font-weight: bold;" >Cause: <span style='color: green;'>${item.call_reason}</span>; 
                                            <span style="margin-left: 10px;"><i class="fa fa-clock-o"></i> ${ new Date(item.duration * 1000).toISOString().substr(14, 5) }</span> </p>
                                            <p>
                                                <span>Direction: <b>${item.call_direction}</b> </span>
                                                <span style="margin-left: 10px;">Status: <b>${item.call_status}</b> </span>
                                            </p>
                                            <p>${item.call_datetime}; By- <b>${item.agent_name}</b></p>
                                            <p class="bg-info" style="padding: 2px 5px;">${item.feedback}</p>

                                        </div>`;
                    });

                  
                    $(".call-history").append(callHistoryDisplay);
                    
                    
                }

                $(".loadCallHistory").remove();

            }

        }

    });

});


JsSIP.debug.disable('JsSIP:*');