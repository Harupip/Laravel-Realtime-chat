const { data } = require('autoprefixer');
var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server, {
    cors: {origin: "*"}
});
var socketioJwt = require('socketio-jwt');
var myEnv = require('dotenv').config({path:'../.env'});


app.get('/', (req, res) => {
    res.sendFile(__dirname + '/myauth.html');
  });

/* 
Accept connection and    authorize token code
*/
io.on('connection', socketioJwt.authorize({
    secret: "B1Bbr8EnAdJASz3l7Wl0U69f4UHsdtDX",
    timeout: 15000

}));

const users = [];
/* 
When authenticated, send back userid + email over socket
*/
io.on('authenticated', function (socket) {


    // console.log(socket.decoded_token);    
    //console.log(socket);    
    //console.log('id', socket.id);
    socket.emit('user-id', socket.decoded_token.userid);
    socket.emit('user-email', socket.decoded_token.email);

    socket.on('chat', function(data, room) {
        if (room === "") {
            io.emit('chat', socket.decoded_token.name + ": " + data );
            
        } else {
            io.to(room).emit('chat', socket.decoded_token.name + ": " + data);
        }
    });

    socket.on('public-my-message', function (data) {
		socket.emit('receive-my-message', data.msg);
    });

    socket.on("join", (room) => {
        socket.join(room);
        console.log(io.sockets.adapter.rooms);
    });

    socket.on("leave", (room) => {
        socket.leave(room);
        console.log(io.sockets.adapter.rooms);
    });

    socket.on('login', function(user_id){
        users[user_id] = socket.id;
        console.log('a user ' + socket.decoded_token.userid + ' connected');
        
        // saving userId to object with socket ID
        
        io.emit('userStatus', users);
        //console.log(users);
        
    });
    
    socket.on('disconnect', function(){
        let i = users.indexOf(socket.id);
        console.log('user ' + users[i] + ' disconnected');
        
        delete users[i];
        io.emit('userStatus', users);
        console.log(users);
        // remove saved socket from users object
       // delete users[socket.id];
    });
});


/* 
Start NodeJS server at port 3000
*/
server.listen(3000, () => {
    console.log('Server is running');
});