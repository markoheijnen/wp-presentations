var orbiter;
var test;

jQuery(document).ready( function() {
  roomID = "chatRoom";
  orbiter = new net.user1.orbiter.Orbiter();
  orbiter.addEventListener(net.user1.orbiter.OrbiterEvent.READY, readyListener, this);
  orbiter.addEventListener(net.user1.orbiter.RoomEvent.JOIN, joinRoomListener, this);
  orbiter.getMessageManager().addMessageListener("CHAT", chatListener, this, [roomID]);

  //orbiter.connect("tryunion.com", 80);
  orbiter.connect("localhost", 9101);
});

function readyListener (e) {
  
  orbiter.createRoom(roomID);
  orbiter.joinRoom(roomID);
}

function joinRoomListener (e) {
  
}

function chatListener (fromClientID, message) {
  if ( test ) {
    if(message == "next") {
    	test.next();
    }
    else if(message == "previous") {
  	  test.prev();
    }
  }
}