//change
var fs = require('fs');
var config = require('./config');
var port = config.get('port');
var web = require('http').createServer(webHandler).listen(port);
var	io = require('socket.io').listen(web);
var redis = require('redis');
var redisClient = redis.createClient();
var MongoClient = require('mongodb').MongoClient;
// ________________________________________________
var Users = { /* 'userId' : {userName: '', sessionId: '', status:'', sockets:[]}, */};
var LockUrlList = {/* url:{ userId: '', startTime:''} */};
var SocketsList = {/* 'socketId': {userId: '', url:''}*/};
var Notification = {
        /* 'bell' : [ {_id: "_id", notif: "notif", type: "type", url: "$url", identity: "identity"} ]*/
        /* 'mail' : [ {_id: "_id", notif: "notif", type: "type", url: "$url", identity: "identity"} ]*/
};
// ________________________________________________
var Distributor = {
    countUsers : 0,
    keyUsers : {},
    stackNotification : {},
    moderatorNotifList: {
        /* 'bell' : [ {_id: "_id", notif: "notif", type: "type", url: "$url", identity: "identity"} ]*/
        /* 'mail' : [ {_id: "_id", notif: "notif", type: "type", url: "$url", identity: "identity"} ]*/
    },

    createStack: function (key) {
        if(Notification[key] !== undefined){
            var notificationLength = Notification[key].length;
            if(notificationLength > 0)
            {
                var userKeys = Object.keys(this.moderatorNotifList);
                var userKeysLength = userKeys.length;
                var userKey = 0;
                var limit = ((20 * userKeysLength) < notificationLength) ? 20 * userKeysLength : 20 ;

                this.stackNotification[key] = [];
                for (var i = 0 ; (i < notificationLength  && i < limit); i ++){
                    if(Notification[key][i] !== undefined){
                        this.stackNotification[key].push(Notification[key][i]);
                    }
                }

                while(this.stackNotification[key].length){
                    for(var iterator = 0; iterator < userKeysLength; iterator++){
                        userKey = userKeys[iterator];
                        var itemElem = this.stackNotification[key].slice(0,1);
                        if(Object.keys(itemElem).length > 0){
                            this.moderatorNotifList[userKey][key].push(itemElem[0]);
                        }
                        this.stackNotification[key].shift();
                    }
                }
            }
        }
    },

    getStack: function (flag) {
        this.countUsers = Object.keys(Users).length;
        this.keyUsers = {};
        this.moderatorNotifList = {};

        for(var iterator = 0; iterator < this.countUsers; iterator++){
            var userKey = Object.keys(Users)[iterator];
            if(Users[userKey].userType == 5){
                this.moderatorNotifList[userKey] = {bell:[], mail:[]};
            }
        }

        if(Object.keys(this.moderatorNotifList).length > 0){
            if(flag === undefined || flag === false){

                var notifKeys = Object.keys(Notification);
                var keyLength = notifKeys.length;
                for (var k = 0; k < keyLength; k++){
                    this.createStack(notifKeys[k]);
                }
            } else {
                this.createStack(flag);
            }
        }
    },
};
// ________________________________________________
Array.prototype.remove = function(value) {var idx = this.indexOf(value);if (idx != -1) {return this.splice(idx, 1);}return false;};
redisClient.on("error", function (err) {console.log("Error: " + err);});
web.on('error', function (e) { if(e){ setTimeout(function () { s.close(); s.listen(port); }, 1000); } });
// _____________________________________________________________
function webHandler(req, res) { // Обработка web запроса
    // res.write('<script>console.info("Users");console.log('+JSON.stringify(Users)+')</script>');
    // res.write('<script>console.info("SocketsList");console.log('+JSON.stringify(SocketsList)+')</script>');
    // res.write('<script>console.info("LockUrlList");console.log('+JSON.stringify(LockUrlList)+')</script>');
    //res.write('<script>console.info("Adverts");console.log('+JSON.stringify(Adverts)+')</script>');
    // res.write('<script>console.info("Distributor");console.log('+JSON.stringify(Distributor)+')</script>');
    // console.log(req.url);
    req.on('data', function(chunk) {
        var postStr = decodeURIComponent(decodeURIComponent(chunk.toString()));
        var arrPost = postStr.split('&');
        var post = {};
        arrPost.forEach(function(i) {
            var temp = i.split('=');
            var name = temp[0];
            var value = temp[1];
            post[name] = value;

        });
       console.log("Пришел пост запрос:", post);
        if(post.action == 'create'){
            if(Notification[post.notif] != undefined){
                Notification[post.notif].push({notif:post.notif, type:post.type, url: post.url, identity_id:post.identity_id});
            } else {
                Notification[post.notif] = [{notif:post.notif, type:post.type, url: post.url, identity_id:post.identity_id}];
            }

        }
        if(post.action == 'update' || post.action == 'delete'){
            if(Notification[post.notif] !== undefined){
                for(var key in Notification[post.notif]){
                    if(Notification[post.notif][key].identity_id == post.identity_id && Notification[post.notif][key].type == post.type) {
                        console.log(post.identity_id);
                        Notification[post.notif].splice(Notification[post.notif].indexOf(Notification[post.notif][key]), 1);
                    }
                }
            }
        }
        if(post.action == 'get_adverts_mongo'){
            getDataFromMongo();
            setTimeout(function(){ updateNotificationStack(false); }, 3000);
            return;
        }
        updateNotificationStack(post.notif);
        res.end();
    });
    res.end();
}
// _____________________________________________________________
function checkUrl(data,socket,notAjax) {
    var itemUrl = data.url.split('/');
    var blockedUrl = ['deception', 'feedback', 'challenge', 'recall', 'article'];
    var pattern = '^([a-zA-Z\-\/]+)\/((?!new)[a-zA-Z0-9\/\-]+)';
    var expr = new RegExp(pattern, 'g');

    if(blockedUrl.indexOf(itemUrl[1]) !== -1 && data.url.search(expr) !== -1)
    {
        if( LockUrlList[data.url] == undefined) {
            // var id; Adverts[id].lock=1;
            LockUrlList[data.url] = {userId: data.userId, startTime: +new Date()};
            SocketsList[socket.id] = {userId: data.userId, url: data.url};
            socket.emit('answerCheckUrl', {access: true, url:data.url, urlChange: data.urlChange});
        }else{
            var acc = true;
            for(var key in SocketsList){
                if(data.url == SocketsList[key].url && socket.id != key){
                    acc = false;
                    break;
                }
            }
            if(acc == false){
                if(notAjax==true){
                    socket.emit('redirect');
                }
                SocketsList[socket.id] = {userId: data.userId, url: '/'};
                //socket.emit('answerCheckUrl', {access: false, userId: data.userId, userName: Users[data.userId].userName});
                socket.emit('answerCheckUrl', {
                    access: false,
                    userId: SocketsList[key].userId,
                    userName: Users[SocketsList[key].userId].userName
                });
            } else {
                SocketsList[socket.id] = {userId: data.userId, url: data.url};
                socket.emit('answerCheckUrl', {access: true, url:data.url, urlChange: data.urlChange});
            }
        }
    } else {
        socket.emit('answerCheckUrl', {access: true, url:data.url, urlChange: data.urlChange});
        SocketsList[socket.id] = {userId: data.userId, url: data.url};
    }
    io.sockets.emit('showUsersInfo', {Users: Users, SocketsList: SocketsList});
}

function updateNotificationStack(flag, newUser) {
    if(Object.keys(Users).length == 0) {console.log('Нет активных юзеров'); return;}
    Distributor.getStack(flag);
    var update = {};
    for(var userId in Users){
        Users[userId].sockets.forEach(function(socketId) {
            if(Distributor.moderatorNotifList[userId] !== undefined){
                update = {flag: flag, stack : Distributor.moderatorNotifList[userId]};
                io.sockets.sockets[socketId].emit('updateNotificationStack', update);
            }
        });
    }
}

// _____________________________________________________________
function decodeAuthKey(authKey, userId) { // Дешифровка ключа авторизации
    authKey = new Buffer(authKey, 'base64').toString("ascii");
    var lengthUserId = userId.length;
    var lenghtAuthKey = authKey.length;
    authKey = authKey.substr(0, lenghtAuthKey - lengthUserId);
    return authKey;
}
// ____________________________
function getDataFromMongo() {  // Загрузка данных с Монго
    MongoClient.connect(config.get('mongo:host')+config.get('mongo:table'), function (err, db) {
        if (err) throw err;
        else {
            var col = db.collection(config.get('mongo:collection:moderate'));
            console.log("Успешно подключились к Монго.");
            col.aggregate([
                {$match:{"create_time":{$gt : 1488240000}}},
                {$sort: {"identity": 1}},
                {
                    $group:{
                        "_id":"$notif",
                        "adverts" : {$push : {"notif":"$notif", "type": "$type", "url":"$url", "identity_id":"$identity_id"}}
                    }
                },
                {
                    $project:{
                        "_id" : 0,
                        "notif" : "$_id",
                        "adverts": "$adverts"
                    }
                },
            ]).each(function(err, item) {
                if (item != null && item.notif !== null) {
                    Notification[item.notif] = item.adverts;
                } else db.close();
            });
            console.log("Успешно загрузили обьявления.");
        }
    });
}
// ______________________________________________
getDataFromMongo();
// ______________________________________________
io.sockets.on('connection', function (socket) { // Событие подключение нового сокета(юзера)


    console.log('Подключился ' , socket.id );
    socket.on('checkAuthKey', function (data) { // Проверка авторизации юзера
        var domain = socket.handshake.headers.origin;
        var url = socket.handshake.headers.referer.replace(domain, '');
        //console.log('Авторизация юзера:', data);console.log('Домен: ',domain);
        if( domain == config.get('domain')){
            redisClient.get(decodeAuthKey(data.authKey, data.userId), function (err, repl) {
                if (err) {console.log('Что то случилось при чтении: ' + err);}
                else if (repl) {
                    // console.log('Ключ в редисе найден: ' + repl);
                    socket.on('checkUrl', function(data) {checkUrl(data, socket);});
                        socket.on('disconnect', function() { // Обработка закрытия сокета
                            console.log(socket.id, ' соединение закрыто');
                            Users[SocketsList[socket.id].userId].sockets.remove(socket.id); // Удаляем сокет с массива для
                            if(Users[SocketsList[socket.id].userId].sockets.length == 0){
                                delete Users[SocketsList[socket.id].userId]; // Если сокет последний, убиваем юзера
                                updateNotificationStack(false);
                            }
                            delete SocketsList[socket.id];
                            io.sockets.emit('showUsersInfo', {Users: Users, SocketsList: SocketsList});
                        });

                        if( Users[data.userId] == undefined ){ // Новый юзер
                            Users[data.userId] = {userName: data.userName, userType: data.userType, sessionId: repl, status:'', sockets:[socket.id]};
                            //console.log('Зарегистрирован новый юзер:', data.userId);
                        }else{ // Новый сокет для существующего юзера
                            Users[data.userId].sockets.push(socket.id);
                            console.log('Юзер '+data.userId+' создал новый сокет:');
                        }
                        updateNotificationStack(false);
                        checkUrl({userId:data.userId, url: url},socket,true);

                } else {console.log('Ключ сесии в редисе ненайден.');socket.disconnect();}
            });
        }else{ console.log('Ошибка авторизации. Неверный домен. userId', data.userId);socket.disconnect(); }
        console.log('__________________________');
    });
});
console.log('Нод сервер запущен __________________________');
