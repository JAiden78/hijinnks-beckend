/**
 * Created by nomantufail on 10/27/2016.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var db = require('./db.js');
var mydb = new db();

app.get('/', function(req, res){
    res.send('dfdf');
});
var sockets = {};
var arr = [];
io.on('connection', function(socket){
    socket.on('message_get', function(data){
        io.emit('message_send', {'user_id':data.user_id,'id':data.id,'other_name':data.other_name,'photo':data.photo,'text':data.text,'other_id':data.other_id,'file':data.file,'file_type':data.file_type,'file_poster':data.file_poster,'images_base':data.images_base,'video_base':data.video_base});
    });
    socket.on('group_message_get', function(data){
        io.emit('group_message_send', {'group_id':data.group_id,'other_name':data.other_name,'photo':data.photo,'text':data.text,'other_id':data.other_id,'file':data.file,'file_type':data.type,'file_poster':data.file_poster,'images_base':data.images_base,'video_base':data.video_base,'message_id':data.message_id
        });
    });
    socket.on('disconnect', function(){
        if(sockets[socket.id] != undefined){
            mydb.releaseRequest(sockets[socket.id].user_id).then(function (result) {
                console.log('disconected: '+sockets[socket.id].request_id);
                io.emit('request-released',{
                    'request_id':sockets[socket.id].request_id
                });
                delete sockets[socket.id];
            });
        }
    });
});

http.listen(5000, function(){
    console.log('listening on *:5000');
});