<x-app-layout>
    <head>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.8/socket.io.js"></script>
        <link href="../style.css" rel="stylesheet">
    </head>
    <body>
    
    
    <h1>Socket Connection Status: <span id="connection"></span></h1>
    <h1>Login User Id: <span id="userid"></span></h1>
    <h1>Login User Email: <span id="email"></span></h1>
    <div class="container" style="border-top: 2px solid black ">
        <div class="row" style="margin-top: 5px">
            <div class="col-2" style="border-right: 2px solid black">
                <ul id="users" class="list-group">
                    @foreach ($users as $user)
                        <li1 class="list-group-item">
                            <div class="chat-name font-weight-bold">
                                <i class="fa fa-circle user-status-icon user-{{$user->id}}" title="Away"></i>  {{$user->name}}
                            </div>
                        </li1>
                    @endforeach
                </ul>
            </div>
            <div class="col-9 border-left border-success">
                <div class="row" style="margin-top: 5px">
                <!-- Button trigger modal -->
                    <div class="col-8">
                        <div style="text-align: center; margin-bottom:  5px;">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="color: pink">
                                Create Room
                            </button>
    
                            <!-- Button trigger modal -->
                            <button disabled id="btn-add-fr" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#participantModal" style="color: pink">
                                Add more friends to room
                            </button>
                        </div>
                    </div>
                    <div class="col-3">
                        <div style="margin-left: 101%;">
                            <form id="leaveRoom" action="">
                                <input hidden id="Lroom" value=""/>
                                <button disabled id="btn-lev" type="button" class="btn btn-danger" onclick="leaveRoom();" style="color: pink;">
                                    Leave
                                </button>
                                {{-- <button disabled id="btn-lev" type="button" class="btn btn-danger"  style="color: pink;">
                                    Leave
                                </button> --}}
                            </form>
                            
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="roomModalLabel">Create Room</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <label class="form-label">Room Name:   </label>
                                    <input name="name" class="form-control mb-2" type="text"
                                        id="room_id" placeholder="~~ Room name ~~" value="">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" style="color: pink;" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" style="color: pink;">Save changes</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </form>

                
                
                
                <!-- Modal -->
                <form action="{{ route('participants.store') }}" method="POST">
                    @csrf
                    <div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="participantModalLabel">More Participant</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="container-fluid">
                                    <label class="form-label">Choose user:   </label>
                                    <input type="hidden" name="room_id" id="rm_id" value="">
                                    <select name="user" class="form-select form-select-sm" aria-label=".form-select-sm example" id="userSelect">
                                        <option value="" disabled selected hidden>Choose user to add </option>
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}">{{ $user->name }}</option>  
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" style="color: pink;" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" style="color: pink;">Save changes</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </form>

                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="roomSelect">
                    <option value="" disabled selected hidden>Choose the room to chat </option>
                    @foreach ($participants as $participant)
                        <option value="{{$participant->id}}">{{ $participant->name }}</option>  
                    @endforeach
                </select>
                
                <form id="joinRoom" action="">
                    <input hidden id="Jroom" autocomplete="off" value="" />
                </form>
                
                <ul id="messages" class="text-wrap"></ul>   
                <div style="height: 48px"></div>
            
                <form id="form">
                    <input disabled placeholder="Chat anything ..." id="input" autocomplete="off" />
                    <button>Send</button>
                </form>
            </div> 
        </div>
        
    </div>
    
    
    <script type="text/javascript">
        $(document).ready(function () {
            let user_id = "{{ auth()->user()->id }}";
            socketIOConnectionUpdate('Requesting JWT Token from Laravel');
    
            $.ajax({
                url: 'http://localhost:8000/token' //set up url
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                socketIOConnectionUpdate('Something is wrong on ajax: ' + textStatus);
            })
            .done(function (result, textStatus, jqXHR) {
    
    
                /* 
                make connection with localhost 3000
                */
                var socket = io.connect('http://localhost:3000');
                var userId = '';
                

                socket.emit('login', user_id);
                /* 
                connect with socket io
                */
                socket.on('connect', function () {
                    socketIOConnectionUpdate('Connected to SocketIO, Authenticating')
                    socket.emit('authenticate', {token: result.token});
                    
                });
    
    
                /* 
                If token authenticated successfully then here will get message 
                */
                socket.on('authenticated', function () {
                    socketIOConnectionUpdate('Authenticated');
                });
    
    
                /* 
                If token unauthorized then here will get message 
                */
                socket.on('unauthorized', function (data) {
                    socketIOConnectionUpdate('Unauthorized, error msg: ' + data.message);
                });
    
    
                /* 
                If disconnect socketio then here will get message 
                */
                socket.on('disconnect', function () {
                    socketIOConnectionUpdate('Disconnected');
                });
    
    
                /* 
                Get Userid by server side emit
                */
                socket.on('user-id', function (data) {
                    userId = data;
                    $('#userid').html(data);
                });
    
    
                /* 
                Get Email by server side emit
                */
                socket.on('user-email', function (data) {
                    $('#email').html(data);
                });
    
                var form = document.getElementById('form');
                var messages = document.getElementById('messages');
                var input = document.getElementById('input');
                var jroomInput = document.getElementById('Jroom');
                var lroomInput = document.getElementById('Lroom');
                var joinRoom = document.getElementById('joinRoom');
                var leaveRoom = document.getElementById('leaveRoom');
                var roomSelect = document.getElementById('roomSelect');
                var roomId = document.getElementById('rm_id');
                var addFrBtn = document.getElementById('btn-add-fr');
                var btnLev = document.getElementById('btn-lev');

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var room = jroomInput.value;
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '/room/'+ roomSelect.value +'/'+ userId +'/messages',
                        type:"POST",
                        data:{
                            message: input.value,
                        },
                        // success:function(response){
                        //     alert('success');
                        // },
                        // error: function(response) {
                        //     alert('error');
                        // },
                    });

                    if (input.value) {
                          socket.emit('chat', input.value, room);
                          input.value = '';
                    }
                });

                roomSelect.addEventListener('change', function(e) {
                    e.preventDefault();
                    roomId.value = roomSelect.value;
                    jroomInput.value = roomSelect.value;
                    document.getElementById('Lroom').setAttribute('value',roomSelect.value);    
                    input.disabled = false;
                    addFrBtn.disabled = false;
                    btnLev.disabled = false;
                    socket.emit("join", jroomInput.value);
                    
                    $('li').remove();
                    $.ajax({
                        type: 'GET', 
                        url: '/room/' + roomSelect.value + '/messages',
                        //dataType: 'json',
                        success: function (data) {
                            $.each(data, function(index, item) {
                                var items = document.createElement('li');
                                items.textContent = item.name + ":  " + item.message;
                                messages.appendChild(items);
                            });
                            window.scrollTo(0, document.body.scrollHeight);
                        },error:function(){ 
                            alert("failed");
                        }
                    });

                    // $.get('/room/' + 1 + '/messages', function(data) {
                    //         alert(JSON.);
                    //         console.log(JSON.stringify(data));
                    // });
                });
                
                // btnLev.addEventListener('click', function(e) {
                //     $.ajax({
                //         url: '/room/users/' + roomSelect.value,
                //         type: 'DELETE',
                //     }); 
                // });
    
                socket.on('chat', function (data) {
                    var item = document.createElement('li');
                    item.textContent = "\n"+ data;
                    messages.appendChild(item);
                    window.scrollTo(0, document.body.scrollHeight);
                });
    
                /* 
                Get receive my message by server side emit
                */
                socket.on('receive-my-message', function (data) {
                    $('#receive-my-message').html(data);
                });

                // joinRoom.addEventListener('submit', function(e) {
                //     e.preventDefault();
                //     var room = jroomInput.value;
                //     socket.emit("join", room);
                // });

                leaveRoom.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var room = lroomInput.value;
                    socket.emit("leave", room);
                });

                // function displayRoom (room) {
                //     var item = document.createElement('li');
                //     item.textContent = room;
                //     document.getElementById("messages").appendChild(item); 
                // }
                
                socket.on('userStatus', function(data) {
                    let userStatusIcon = $('.user-status-icon');
                    userStatusIcon.removeClass('text-success');
                    userStatusIcon.attr('title', 'Away');

                    $.each(data, function(key,value) {
                        if(value != null && value != 0) {
                            let userIcon = $('.user-' + key);
                            userIcon.attr('title', 'Online');
                            userIcon.addClass('text-success');
                        }
                    });
                });
            });
        });
    
    
        /* 
        Function for print connection message
        */
        function socketIOConnectionUpdate(str) {
            $('#connection').html(str);
        }

        function leaveRoom() {
            location.href = "/room/users/" + roomSelect.value ;
        }

        
    </script>
    </body>

    
</x-app-layout>
