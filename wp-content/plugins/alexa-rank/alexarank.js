function getAlexaRankRpcUrl() {
    var urls = ['http://phprpc-gpr.appspot.com/',
                'http://phprpc-gpr1.appspot.com/',
                'http://phprpc-gpr2.appspot.com/',
                'http://phprpc-gpr3.appspot.com/',
                'http://phprpc-gpr4.appspot.com/',
                'http://phprpc-gpr5.appspot.com/',
                'http://phprpc-gpr6.appspot.com/',
                'http://phprpc-gpr7.appspot.com/',
                'http://phprpc-gpr8.appspot.com/',
                'http://phprpc-gpr9.appspot.com/']
    return urls[Math.floor(Math.random() * urls.length)];
}

var alexarank_rpc = new PHPRPC_Client(getAlexaRankRpcUrl(), ['alexarank']);

function showAlexaRank(result) {
        var alexa_container = document.getElementById('alexa_container');
        var alexa_bar = document.getElementById('alexa_bar');
        var alexa_rank = document.getElementById('alexa_rank');
        alexa_container.title = alexa_bar.title = "Alexa Rank: " + (result <= 0 ? 'n/a' : result) + ' | Powered by PHPRPC';
        var rank = 38;
        if (result > 0) {
            rank = Math.log(result) / Math.log(5);
            rank = Math.floor(rank);
            if (rank > 10) {
                rank = 10;
            }
        }
        alexa_rank.style.width = ((10 - rank) << 2) + "px";
}

function AlexaRank() {
    result = document.cookie.match(/alexarank=([^;]*);/);
    if (result) {
        result = unescape(result[1]);
        showAlexaRank(result);
    }
    else {
        alexarank_rpc.alexarank(location.href, function (result) {
            result = parseInt(result);
            if (isNaN(result)) return;
            var exp = new Date();
            exp.setTime(exp.getTime() + 86400000);
            document.cookie = "alexarank=" + escape(result) + "; " +
                              "expires=" + exp.toGMTString() + "; " +
                              "path=/;";
            showAlexaRank(result);
            alexarank_rpc.dispose();
            alexarank_rpc = null;
        });
    }
}