    </div>
    <!-- /.dynamic-containter -->

    <div class="chatBox">

        <!-- <div class="chatBoxItem chatBoxForUser12">
            <input type="hidden" value="12" class="userId">
            <div class="header">
                <img width="40px" height="40px"
                    src="http://192.168.10.133/bumsys/images/?for=employees&amp;id=undefined" class="img-circle">
                <div class="card">
                    <p class="name">A.K.M. Aminul Haque</p>
                    <p>System Engineer</p>
                </div>
                <div class="action">
                    <span class="close-chat">
                        <i class="fa fa-close"></i>
                    </span>
                </div>
            </div>
            <div class="direct-chat direct-chat-warning">

                <div class="direct-chat-messages">
                    <div class="direct-chat-msg">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name pull-left">KHADIM MD. KHURSHID ALAM</span>
                            <span class="direct-chat-timestamp pull-right">2022-12-25 11:49:58</span>
                        </div>
                        <img class="direct-chat-img" src="http://192.168.10.133/bumsys/images/?for=employees&amp;id=1">
                        <div class="direct-chat-text">
                            sdf
                        </div>
                    </div>
                 
                </div>
            </div>
            <div class="composer">
                <label for="chatAttachmentundefined">
                    <span title="Attach file" class="attachment">
                        <i class="fa fa-plus"></i>
                    </span>
                </label>
                <input style="display: none;" type="file" class="chat-attachment" id="chatAttachmentundefined">
                
                <div class="message-composer">

                    <div class="attachment-viewer">
                       
                        <!-- <div class="attachmentItem">
                            <img src="http://192.168.10.133/bumsys/images/?for=employees&id=1&v=17" alt="">
                            <span title="Remove this attachment" class="remove-attachment">
                                <i class="fa fa-close"></i>
                            </span>
                       </div>
                       <div class="attachmentItem">
                            <div class="files-extension">
                                <i class="fa fa-file"></i>
                                <p class="extension-name">PDF</p>
                            </div>
                            <span title="Remove this attachment" class="remove-attachment">
                                <i class="fa fa-close"></i>
                            </span>
                       </div> 
                
                      
                    </div>
                    
                    <input type="text" placeholder="Type message and press enter to send ..." class="form-control message-input">
                </div>
                
            </div>
        </div> -->


    </div>


    <style>
.chatBox {
    position: fixed;
    right: 25px;
    bottom: 0;
    display: inline;
    z-index: 100;
}

.chatBox .chatBoxItem {
    display: inline-block;
    margin-left: 10px;
}

.chatBoxItem {
    position: relative;
    height: 480px;
    width: 360px;
    background-color: #fff;
    border: 1px solid #dedede;
    border-radius: 10px 10px 0 0;
    box-shadow: 0px -4px 15px -5px rgba(0, 0, 0, 0.1);
}

.chatBoxItem .header {
    padding: 10px;
    height: 62px;
    border-bottom: 1px solid #dedede;
    box-shadow: 0px 3px 5px -3px rgba(0, 0, 0, 0.1);
    width: 360px;
}

.chatBoxItem .header * {
    display: inline-block;
    vertical-align: middle;

}

.chatBoxItem .header .card {
    margin-left: 15px;
}

.chatBoxItem .header .card p {
    display: block;
    margin: 0;
}

.chatBoxItem .header .card .name {
    font-size: 16px;
    font-weight: bold;

}

.chatBoxItem .header .action {
    position: absolute;
    font-size: 20px;
    display: inline;
    right: 10px;
}

.chatBoxItem .header .action span {
    padding: 5px 8px;
    cursor: pointer;
}

.chatBoxItem .header .action span:hover {
    background-color: #e9e9e9;
    border-radius: 10px;
}


.chatBoxItem .composer {
    bottom: 10px;
    position: absolute;
    width: 100%;
}


.chatBoxItem .composer * {
    display: inline-block;
    vertical-align: bottom;
    margin: 0;
}

.composer .message-composer {
    width: 310px;
    border-radius: 10px;
    background-color: #f5f5f5;
    border-color: #f5f5f5;
}

.composer .attachment {
    padding: 10px;
    cursor: pointer;
    max-height: 220px;
    overflow: auto;
}

.message-input, .message-input:focus {
    background-color: transparent;
    border-color: transparent;
}

.message-composer .attachment-viewer {
    max-height: 230px;
    overflow: auto;
    display: flex;
    flex-wrap: wrap;
}

.attachmentItem {
    background-color: #e7e7e7;
    width: 62px;
    height: 62px;
    border-radius: 10px;
    margin: 10px 3px 3px 10px !important;
    position: relative;
}

.attachmentItem img {
    width: 62px;
    height: 62px;
    border-radius: 10px;
}

.attachmentItem .files-extension {
    position: relative;
    font-size: 25px;
    line-height: 0;
    text-align: center;
    padding-top: 10px;
    width: 62px;
    height: 62px;
}

.files-extension .extension-name {
    font-size: 14px;
    display: block;
    margin-top: 10px;
}

.attachmentItem .remove-attachment {
    background-color: black;
    border-radius: 10px;
    position: absolute;
    right: -5px;
    top: -5px;
    color: white;
    width: 20px;
    height: 20px;
    text-align: center;
}

.attachmentItem .remove-attachment .fa-close {
    vertical-align: baseline !important;
    cursor: pointer;
}



    </style>



    <?php require "modals.php"; ?>
    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">
            Version 2.2.0
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; <?php echo date("Y"); ?> <a href="https://bumsys.org/"><span
                    class="logo-lg"><b>bum</b>sys</span></a>.</strong> All rights reserved.
    </footer>

    </div>
    <!-- ./wrapper -->

    <!-- Include all Header JS -->
    <script src="<?php echo full_website_address(); ?>/js/?q=foot&v=2.2.0"></script>

<!-- 
    <script>
$(document).on("keyup", ".message-composer", function(e) {

    if (e.key === "Enter" && $(this).val() !== "") {

        // Send the message
        var message = $(this).val();

        // Clear the input filed after send
        $(this).val("");

        var datetime = BMS.fn.getDateTime();

        var chatHtml = `<div class="direct-chat-msg">
                                    <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-left"><?php echo $empFullName; ?></span>
                                        <span class="direct-chat-timestamp pull-right">${datetime}</span>
                                    </div>
                                    <img class="direct-chat-img" src="<?php echo full_website_address(); ?>/images/?for=employees&id=<?php echo $_SESSION["uid"]; ?>">
                                    <div class="direct-chat-text">
                                        ${message}
                                    </div>
                                </div>`;


        var container = $(this).closest(".chatBoxItem").find(".direct-chat-messages");

        $(container).append(chatHtml);

        // Send the message to remote user
        var userId = $(this).closest(".chatBoxItem").find(".userId").val();
        BMS.CHAT.send(message, userId);

        // Scroll down to show the last message
        var chatHeight = $(container).get(0).scrollHeight;
        $(container).animate({
            scrollTop: chatHeight
        }, 0);

    }

});

function showIncomingMsg(data) {

    var isChatBoxOpened = $(`.chatBoxForUser${data.fromUser}`).length > 0;

    if (isChatBoxOpened === false) {

        BMS.CHAT.showChatBox("", data.fromUser);

    } else {

        var datetime = BMS.fn.getDateTime();
        var chatHtml = `<div class="direct-chat-msg right">
                                    <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-right">${data.fromUserName}</span>
                                        <span class="direct-chat-timestamp pull-left">${datetime}</span>
                                    </div>
                                    <img class="direct-chat-img" src="${full_website_address}/images/?for=employees&id=${data.fromUser}">
                                    <div class="direct-chat-text">
                                        ${data.msg}
                                    </div>
                                </div>`;

        var container = $(`.chatBoxForUser${data.fromUser}`).find(".direct-chat-messages");
        $(container).append(chatHtml);

        var chatHeight = $(container).get(0).scrollHeight;
        $(container).animate({
            scrollTop: chatHeight
        }, 0);

    }


}


// Remove current
$(document).on("click", ".close-chat", function() {

    $(this).closest(".chatBoxItem").remove();


});

// // Connect web socket
// var wss = new WebSocket('ws://192.168.10.133:8081/chat');

// wss.onmessage = function(e) {

//     // If there is no data
//     if (e.data === "") {
//         return;
//     }

//     var data = JSON.parse(e.data);

//     console.log(data);


//     if (data.type === "message") {

//         showIncomingMsg(data);

//     }


// }


// // Add the user to websocket when open
// wss.onopen = function(e) {

//     wss.send(
//         JSON.stringify({
//             "localUserId": '<?php echo $_SESSION["uid"]; ?>',
//             "localUserName": '<?php echo $empFullName; ?>',
//             "type": "newUser"
//         })
//     );

// };


// Preview file when select
$(document).on("change", ".chat-attachment", function() {

    var file = this.files[0];
    var fileType = file.type;
    var imageExtension = ["image/jpeg", "image/png", "image/jpg"];
    var previewContainer = $(this).closest(".composer").find(".attachment-viewer");

    console.log( previewContainer );

    // Retrieve the extension
    var extension = file["name"].split(".");
    var extension_name = extension[ extension.length - 1 ];
    

    // If file type is image then preview it
    if (imageExtension.includes(fileType) !== false) {

        var reader = new FileReader();
        reader.onload = function(e) {
            $(previewContainer).append(`<div class="attachmentItem">
                            <img src="${e.target.result}" alt="">
                            <span title="Remove this attachment" class="remove-attachment">
                                <i class="fa fa-close"></i>
                            </span>
                       </div>`);
        };

        reader.readAsDataURL(file);


    } else {

        $(previewContainer).append(`<div class="attachmentItem">
                            <div class="files-extension">
                                <i class="fa fa-file"></i>
                                <p class="extension-name">${extension_name}</p>
                            </div>
                            <span title="Remove this attachment" class="remove-attachment">
                                <i class="fa fa-close"></i>
                            </span>
                       </div>`);

    }


});



/** Remove the attachment */
$(document).on("click", ".remove-attachment", function() {

    $(this).closest(".attachmentItem").remove();

});



// //peer = new RTCPeerConnection(configuration);
// peer = new RTCPeerConnection();

// // Create the data channel
// dataChannel = peer.createDataChannel("chatDataChannel", {
//     reliable: true
// });

// var configuration = {
//     "iceServers": [
//         {
//             urls: "stun:openrelay.metered.ca:80",
//         },
//         {
//             urls: "turn:openrelay.metered.ca:80",
//             username: "openrelayproject",
//             credential: "openrelayproject",
//         },
//         {
//             urls: "turn:openrelay.metered.ca:443",
//             username: "openrelayproject",
//             credential: "openrelayproject",
//         },
//         {
//             urls: "turn:openrelay.metered.ca:443?transport=tcp",
//             username: "openrelayproject",
//             credential: "openrelayproject",
//         },
//     ]
// };


// var peer;
// var dataChannel;



// wss.onmessage = function(e) { 

//     if(e.data === "") {
//         return;
//     }

//     var data = JSON.parse( e.data );

//     //console.log( data );

//     /**
//      * If the remote peer asked for my ice candidate
//      */
//     if( data.type === "wantToCall" ) {

//         peer.onicecandidate = function (event) {

//             // Send local peer ice candidate to the remote for offering
//             wss.send(JSON.stringify( 
//                 {
//                     "type": "offerSDP",
//                     "sdp": peer.localDescription,
//                     "localUserId": <?php echo $_SESSION["uid"]; ?>,
//                     "remoteUserId": data.localUserId // The remote user id is set to localUserId when sent from remote
//                 }
//             ));

//             //console.log( peer.localDescription );

//         }

//         // Create the offer
//         peer.createOffer( function(offer) {

//             peer.setLocalDescription(offer);

//         }, function(error) {
//             console.log( error );
//         });


//     } else if( data.type === "offerSDP" ) {

//         // Set the remote description
//         peer.setRemoteDescription( new RTCSessionDescription(data.sdp) );

//         // Create answer
//         peer.createAnswer( function (answer) {

//             peer.setLocalDescription(answer);

//             wss.send(JSON.stringify( 
//                 {
//                     "type": "answerSDP",
//                     "sdp": answer,
//                     "localUserId": <?php echo $_SESSION["uid"]; ?>,
//                     "remoteUserId": data.localUserId // The remote user id is set to localUserId when sent from remote
//                 }
//             ));

//         }, function (error) {

//             alert("oops...error");

//         });

//     } else if( data.type === "answerSDP" ) {

//         var state = peer.connectionState;

//         // If the peer is not connected then connect
//         if( state !== "connected" ) {

//             peer.setRemoteDescription( new RTCSessionDescription(data.sdp) );

//         }

//         console.log( JSON.stringify(data.sdp) );

//     }


// };


// wss.onopen = function(e) { 

//     wss.send(
//         JSON.stringify({ 
//             "localUserId": <?php echo $_SESSION["uid"]; ?>, 
//             "type": "newUser" 
//         })
//     );

// };

// peer.ondatachannel = function (event) {

//     console.log(event.channel);

//     var receiveChannel = event.channel;
//     receiveChannel.onmessage = function (event) {
//         console.log( event.data);
//     };

//     receiveChannel.onopen = function (e) {
//         console.log("opened");
//     }

// };

// function send(msg) {
//     dataChannel.send(msg);
// }


// // function initiateChat() {

//     // wss.send(

//     //     JSON.stringify(
//     //         {
//     //             "type": "wantToConnect",
//     //             "localUserId": 13,
//     //             "remoteUserId": 1
//     //         }
//     //     )

//     // )

// // }
    </script>
 -->

    </body>

    </html>