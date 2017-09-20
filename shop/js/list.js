//左右侧滚动
function HomeScroll(a, c) {
    if ($(a).length > 0 && $(c).length > 0) {
        var f = $(a),
        b = $(c),
        d = f.offset().top,
        e = b.offset().top;
        function g() {
            var k = $(window).scrollLeft(),
            r = $(window).scrollTop(),
            i = $(document).height(),
            o = $(window).height(),
            l = f.height(),
            j = b.height(),
            m = l > j ? e: d,
            n = l > j ? b: f,
            p = l > j ? b.offset().left : f.offset().left ,
            h = l > j ? j: l,
            q = l > j ? l: j,
            s = parseInt(q - o) - parseInt(h - o);
            $(a + "," + c).removeAttr("style");
            if (i < o || h > q || r < m || r <= (h - o + m)) {
                n.removeAttr("style")
            } else {
                if (o > h && (r - m) >= s || o < h && (r - m) >= (q - o)) {
                    n.attr("style", "margin-top:" + s + "px;")
                } else {
                    n.attr("style", "_margin-top:" + (r - m) + "px;position:fixed;left:" + p + "px;" + (o > h ? "top": "bottom") + ":0;")
                }
            }
        }
        $(window).resize(g).scroll(g).trigger("resize")
    }
};

$(function(){
	HomeScroll(".list-left", ".list-right");//左右侧滚动
	
})