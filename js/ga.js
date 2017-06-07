(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-72836905-1', 'auto');
var page = document.location.pathname;
ga('set', 'page', page = page.indexOf('/zh-tw/') > -1 ? page.replace('/zh-tw/', '/') : page.indexOf('/en-us/') > -1 ? page.replace('/en-us/', '/') : page);
ga('send', 'pageview');