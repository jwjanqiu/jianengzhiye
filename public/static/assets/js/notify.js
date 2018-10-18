var mp3 = $("#mp3")[0];
var play = 0;
if(sessionStorage.num){
    document.getElementById("remind").innerText = sessionStorage.num;
}
$.ajax({
    url:"realTimeNotify",
    success:function (data) {
        sessionStorage.num = data;
        document.getElementById("remind").innerText = data;
        play = data;
        if(play > 0){
            mp3.play();
        }
    }
})
setInterval(function () {
    $.ajax({
        url:"realTimeNotify",
        success:function (data) {
            sessionStorage.num = data;
            document.getElementById("remind").innerText = data;
            play = data;
            if(play > 0){
                mp3.play();
            }
        }
    })
},180000)