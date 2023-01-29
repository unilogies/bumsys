if(typeof sipCredentials === "undefined") {
    throw new Error("Sorry! No SIP credentials found");
}

// Set getUserMedia for all browsers
navigator.getUserMedia = ( navigator.getUserMedia ||
                            navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia ||
                            navigator.msGetUserMedia
                        );

// Check if there have microphone connected
//var userMedia = navigator.getUserMedia() || navigator.mediaDevices.getUserMedia();
navigator.getUserMedia(
    {audio: true},
    function(){
        
        // success callback
        $("#status").html("Dail a Number!");

        // enable call button
        $("#callButton").prop("disabled", false);

    }, 
    function() {
        // error callback, no microphone
        $("#status").html("<div style='font-size: 14px;' class='alert alert-danger'>No microphone found! Please connect your mic and reload!</div>");

        // disable call button
        $("#callButton").prop("disabled", true);
        return;
    }
);


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

/*

const audioContext = new AudioContext();
audioParams_01 = {
    deviceId: "default",
}
audioParams_02 = {
    deviceId: "7079081697e1bb3596fad96a1410ef3de71d8ccffa419f4a5f75534c73dd16b5",
}

mediaStream_01 = await navigator.mediaDevices.getUserMedia({ audio: audioParams_01 });
mediaStream_02 = await navigator.mediaDevices.getUserMedia({ audio: audioParams_02 });

audioIn_01 = audioContext.createMediaStreamSource(mediaStream_01);
audioIn_02 = audioContext.createMediaStreamSource(mediaStream_02);

dest = audioContext.createMediaStreamDestination();

audioIn_01.connect(dest);
audioIn_02.connect(dest);

const recorder = new MediaRecorder(dest.stream);

chunks = [];

recorder.onstart = async (event) => {
    // your code here
}

recorder.ondataavailable = (event) => {       
    chunks.push(event.data); 
}

recorder.onstop = async (event) => {
    // your code here
}*/

/** Test */

var callOptions = {
  mediaConstraints: {audio: true, video: false}
};


var caller = "";
var callerDisplay = "";
var hideNumbers = ['*78', '*79', '*76'];

var phone = new JsSIP.UA(configuration);
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

    session.on('ended', function() {

        // Do not show/ Add the caller details for hidden numbers
        if(  jQuery.inArray(caller, hideNumbers) < 0 ) {
        
            addCallLog({
                caller: caller,
                direction: session.direction,
                duration: Math.floor( (session.end_time.getTime() - session.start_time.getTime()) / 1000),
                status: "Answered"
            });

            // Show the call dailer after session end
            $("#callDailer").show();

            // Hide call Received
            $("#callReceiver").hide();

            // Pause Ring
            BMS.fn.pause();

            // Show the call button
            $("#callButton").show();

            // Hide the end button
            $("#hangButton").hide();

            // Stop Timer
            BMS.fn.stopTimer(window.timer);

            $("#status").html(`Dail a number!`);

            // clear case search list
            $(".caseList input[type=search]").val('');
            $(".caseList input[type=search]").trigger("keyup");

            $("#timer").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(500).fadeIn(500, function() {
                
                // Reset and hide the timer
                $("#timer").html("00:00:00");
                $("#timer").hide();

            });

        }

        // Complete the session
        completeSession();
        
    });


    session.on('failed', function(e) {
        
        // Show the call dailer after session end
        $("#callDailer").show();

        // Hide call Received
        $("#callReceiver").hide();

        // Pause Ring
        BMS.fn.pause();

        // Show the call button
        $("#callButton").show();

        // Hide the end button
        $("#hangButton").hide();

        if(session.direction === 'incoming' ){


            if( e.originator === "local" ) {

                $("#status").fadeOut(100).fadeIn(100).fadeOut(150).fadeIn(150).fadeOut(200).fadeIn(200).fadeOut(250).fadeIn(250, function() {
                    
                    $("#status").html(`Call rejected!`);

                    addCallLog({
                        caller: caller,
                        direction: 'incoming',
                        duration: 0,
                        status: "Rejected"
                    });

                });

            } else {

                $("#status").html(`Missed call from ${callerDisplay}!`);
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
                    
                    $("#status").html(`Dail a number!`);

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
                        
                        $("#status").html(`The number is not answered!`);

                        addCallLog({
                            caller: caller,
                            direction: 'outgoing',
                            duration: 0,
                            status: "Not Answered"
                        });

                    } else {
                        
                        $("#status").html(`The number is busy!`);
                        addCallLog({
                            caller: caller,
                            direction: 'outgoing',
                            duration: 0,
                            status: "Busy"
                        });

                    }

                });

            }

        }
        
        completeSession();
        
    });

    session.on("hold", function(e) {

        if(e.originator === "remote") {
            
            $("#status").html(`${caller} is hold you.`);

        } else {
            
            $("#status").html(`${caller} is on hold.`);

        }

    });

    session.on("muted", function(e) {
        
        $("#status").html(`Call is muted.`);

    });

    session.on("unmuted", function(e) {
        
        $("#status").html(`${caller} is connected.`);

    });

    session.on("unhold", function() {
    
        $("#status").html(`${caller} is connected.`);

    });

    session.on('accepted', function(){

        // Do not show the caller details for hidden numbers
        if(  jQuery.inArray(caller, hideNumbers) < 0 ) {

            // Show timer
            $("#timer").show();
            window.timer = BMS.fn.startTimer();

            $("#status").html(`${caller} is connected.`);

            // Show number in sms receipient
            $("#csSmsSendTo").val(caller);

            // Show number in feedback caller
            $("#feedbackCaller").val(caller);
            
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

        $("#status").html(`${callerDisplay} is calling...`);

        // Show the call Receiver
        $("#callReceiver").show();

        // Hide timer
        $("#timer").hide();

        // Hide Call dailer
        $("#callDailer").hide();

        // Show the caller details
        showCallerDetails(caller);


    } else {

        session.connection.addEventListener('addstream', function(e){
            BMS.fn.pause();
            remoteAudio.srcObject = e.stream;
        });     

    }

});


phone.start();
var session;

// Call to the inputed number
$(document).on("click", "#callButton", function() {

    var number = $("#callNumber").val();
    // BMS.fn.play("outgoing_call", true);
    session = phone.call(number, callOptions);

    $("#status").html(`Call is being send to ${number}...`);

    // hide the call button
    $("#callButton").hide();

    // Show the end button
    $("#hangButton").show();

    // Show the caller details
    showCallerDetails(caller);

    // Empty the feedback area and remove previous fired for savings
    $("#feedbackArea, #feedbackReviewer").val("");
    $(document).off("change", "#feedbackArea, #feedbackReviewer");

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

/** Add Shadow after scroll on customer support div */
$(document).scroll(function(e){

   if( $(window).scrollTop() > 20 ) {
        
        $("#customerSupport").css("box-shadow", "rgba(0, 0, 0, 0.07) 0px 1px 1px, rgba(0, 0, 0, 0.07) 0px 2px 2px, rgba(0, 0, 0, 0.07) 0px 4px 4px, rgba(0, 0, 0, 0.07) 0px 8px 8px, rgba(0, 0, 0, 0.07) 0px 16px 16px");
        $("#customerSupport section").slideUp("fast", function() {
            $(this).hide();
        });

   } else {
    
        $("#customerSupport").css("box-shadow", "none");
        $("#customerSupport section").slideDown("fast", function() {
            $(this).show();
        });
   }

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
            feedback: $("#feedbackArea").val(),
            reviewer: $("#feedbackReviewer").val()
        },
        success: function(data, status) {
        }

    });

}


$(document).on("click", '#saveFeedback', function() {

    // Update the call feedback
    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=updateCallFeedback",
        type: "post",
        data: {
            caller: $("#feedbackCaller").val(),
            feedback: $("#feedbackArea").val()
        },
        success: function(data, status) {
            
            if(status = "success" && data.includes('success') ) {

                // Success Altert
                BMS.fn.alertSuccess( $(data).text() , false);

                // Make empty the boxes of feeback
                $("#feedbackCaller").val("");
                $("#feedbackArea").val("");


            } else {

                BMS.fn.alertError($(data).text());

            }
                
        }
    });
        
});


// Insert quick feedback in feedback box
$(document).on("click", ".quickFeedback > ul > li:not(:last-child)", function() {
    
    $("#feedbackArea").val( $("#feedbackArea").val() + $(this).text() + "\n" );

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
$(document).on('keypress', '#callNumber', function(e){

    if(e.key === "Enter") {

        var number = $(this).val();

        if( number.length < 2 ) {
            alert("Please enter a valid number to continue");
        } else {
            showCallerDetails(number)
        }

    }

})

function showCallerDetails(number) {

    // Search case list
    $(".caseList input[type=search]").val(number);
    $(".caseList input[type=search]").trigger("keyup");

    // Search Person list
    $(".personList input[type=search]").val(number);
    $(".personList input[type=search]").trigger("keyup");

    // Show the caller details
    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=getCallerDetails",
        type: "post",
        data: {
            "caller": number
        },
        success: function(data, status) {
            
            if(status == "success") {

                var callerData = JSON.parse(data);

                if(Object.keys(callerData.details).length !== 0) {

                    // Notifiy only on incoming session
                    if( session && session.direction === 'incoming' ) {

                        if(callerData.details.type !== undefined) {
                            BMS.fn.desktopNotify(`${callerData.details.name} (${number}) is calling...`, full_website_address +"/assets/images/call.png", `${callerData.details.type}, ${callerData.details.designation}`);
                        } else {
                            BMS.fn.desktopNotify(`${number} is calling...`, full_website_address +"/assets/images/call.png", "Click Here to open the dashboard");
                        }

                    }
                    

                    $(".callerInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html( callerData.details.name );
                    $(".callerInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html( callerData.details.type );
                    $(".callerInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html( callerData.details.designation );
                    $(".callerInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html( callerData.details.address );

                    var lastCallDetails = "There is no call";
                    if(callerData.agentName !== "") {
                        lastCallDetails = `By <b>${callerData.agentName}</b>, at ${callerData.lastCallTime}; ${callerData.call_status} (${callerData.totalCallCount} total call)`;
                    }

                    $(".callerInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html( lastCallDetails );

                } else {

                    $(".callerInfo > tbody > tr:nth-child(1) > td:nth-child(2)").html("No data found in database");
                    $(".callerInfo > tbody > tr:nth-child(2) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(3) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(4) > td:nth-child(2)").html("");
                    $(".callerInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html("");

                    var lastCallDetails = "There is no call";
                    if(callerData.agentName !== "") {
                        lastCallDetails = `By <b>${callerData.agentName}</b>, at ${callerData.lastCallTime}; ${callerData.call_status} (${callerData.totalCallCount} total call)`;
                    }

                    $(".callerInfo > tbody > tr:nth-child(5) > td:nth-child(2)").html( lastCallDetails );

                    // Show Desktop Notification
                    if( session && session.direction === 'incoming' ) { 
                        BMS.fn.desktopNotify(`${number} is calling...`, full_website_address +"/assets/images/call.png", "Click Here to open the dashboard");
                    }
                    

                }

            }
        }

    });

}


JsSIP.debug.disable('JsSIP:*');