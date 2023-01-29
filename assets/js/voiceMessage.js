if(typeof sipCredentials === "undefined") {

    $(`.voiceMessageTerminal`).append(`<li style='color: red;'>Sorry! No SIP credentials found.</li>`);
    throw new Error("Sorry! No SIP credentials found");

}

var socket = new JsSIP.WebSocketInterface(sipCredentials.socket);
var configuration = {
  sockets  : [ socket ],
  'uri': sipCredentials.uri, 
  'password': sipCredentials.pass,
  'username': sipCredentials.user,
  'register': true
};


var allContactBrodcasted = false;

var phone = new JsSIP.UA(configuration);

var session;
phone.on('newRTCSession', function(data){

    session = data.session;

});


function sendVoiceMessage(number, audio, vm_id=""){

    const voiceCall = new Audio(`${full_website_address}/assets/upload/media/sounds/voice-message/${audio}`);

    // nill the sound in computer. But it will play normal sound on other side
    voiceCall.volume = 0.000001;

    voiceCall.onloadeddata = function() {

        var callOptions = {
            mediaConstraints: {audio: true, video: false},
            mediaStream: voiceCall.captureStream(),
        };

        var completeSession = function(){
            session = null;
        };
    
        // making the call
        phone.call(number, callOptions);
    
        session.on('confirmed',function(){
            
            voiceCall.play();
            
            // Terminate the call after ending of playing the audio
            voiceCall.onended = function() {

                BMS.fn.stopTimer(window['timer'+number]);

                $(`.voiceMessageTerminal > li.${number}`).append("Played.");

                session.terminate();

            };

            $(`.voiceMessageTerminal > li.${number}`).append("Playing... <span class='timer'></span> ");
            window['timer'+number] = BMS.fn.startTimer(`li.${number}>.timer`);
    
        });

        session.on('ended', function(e) {

    
            voiceCall.pause();
            voiceCall.currentTime = 0;

            BMS.fn.stopTimer(window['timer'+number]);

            $(`.voiceMessageTerminal > li.${number}`).append("Ended...");
            

            
            updateCallLog({
                vm_id: vm_id,
                number: number,
                duration: Math.floor( (session.end_time.getTime() - session.start_time.getTime()) / 1000),
                status: "Answered"
            }); 
            
            completeSession();

            if( allContactBrodcasted === false ) {

                // Do next call
                $(document).trigger("initiateNextCall");

            }

            
        });
    
        session.on('failed', function(e) {

            if( e.cause === "SIP Failure Code" ) {
                        
                $(`.voiceMessageTerminal > li.${number}`).append(`Not answered!`);

                updateCallLog({
                    vm_id: vm_id,
                    number: number,
                    duration: 0,
                    status: "Not Answered"
                });

            } else {
                
                $(`.voiceMessageTerminal > li.${number}`).append("Busy!");

                updateCallLog({
                    vm_id: vm_id,
                    number: number,
                    duration: 0,
                    status: "Busy"
                });

            }
    
            completeSession();

            if( allContactBrodcasted === false ) {

                // Do next call
                $(document).trigger("initiateNextCall");

            }
            
        });

        session.on("icecandidate", function(candidate, ready) {

            //console.log(candidate.candidate.candidate);
            
            candidate.ready();

        });

    };
    
}

phone.start();



// Start sending voice message
var initiated = false;
$(document).on("click", ".startSendingVoiceMessage", function() {

    if(initiated == false) {
        
        initiated = true;
        $(this).prop("disabled", true);

        $(".voiceMessageTerminal").append("<li>Initiating... Please wait.</li>");

        var vm_id = $(this).val();

        // Reterving contacts
        $.ajax({
            url: full_website_address + "/xhr/?module=customer-support&page=getVoiceMessageContact",
            type: "post",
            data: {
                id: $(this).val()
            },
            success: function(data, status) {

                data = JSON.parse(data);

                if(status == "success") {

                    $(".voiceMessageTerminal").append(`<li>${data.description} is starting to send...</li>`);

                    // Start sending to first three numbers
                    var totalChanel = 2;
                    var totalContacts = data.contacts.length;
                    var maxDail = totalChanel < totalContacts ? totalChanel : totalContacts;

                    var currentPosition = maxDail;

                    for(var x=0; x<maxDail; x++ ){
                            
                        $(".voiceMessageTerminal").append(`<li class='${data.contacts[x]}'>${data.description} is sending to ${data.contacts[x]}. </li>`);
                        sendVoiceMessage(data.contacts[x], data.record, vm_id);
                        
                    }

                    if(totalContacts < 1) {
                        $(".voiceMessageTerminal").append(`<li style='color: red'>Sorry! ${data.description} has no contacts or already sent.</li>`);
                    }


                    $(document).on("initiateNextCall", function() {

                        // Mark as all contacts have daild
                        if( currentPosition === totalContacts ) {
                            
                            allContactBrodcasted = true;

                            $(".voiceMessageTerminal").append(`<li style='color: green'>${data.description} has been brodcasted to all given contacts.</li>`);

                        } else {

                            $(".voiceMessageTerminal").append(`<li class='${data.contacts[currentPosition]}'>${data.description} is sending to ${data.contacts[currentPosition]}. </li>`);
                            sendVoiceMessage(data.contacts[currentPosition], data.record, vm_id);

                            currentPosition++;

                        }

                    });

                }

            }
        });

    } else {
        
        $(`.voiceMessageTerminal`).append(`<li style='color: red;'>Already brodcasting a voice message.</li>`);

    }
    

});


function updateCallLog({vm_id="", number="", duration="", status=""}="") {

    
    $.ajax({
        url: full_website_address + "/xhr/?module=customer-support&page=updateCallLog",
        type: "post",
        data:{
            vm_id: vm_id,
            number: number,
            duration: duration,
            status: status
        },
        success: function(status, data){}

    });

}

JsSIP.debug.disable('JsSIP:*');