var href, n, s,
    breadcrumb = "";

href = document.location.href;
n = href.indexOf('?');
if (n !== -1) {
    href = href.substring(0, n);
} else {
    href = href.substring(0, href.length);
}

n = href.indexOf('#');
if (n !== -1) {
    href = href.substring(0, n);
} else {
    href = href.substring(0, href.length);
}

s = href.split("/");
s.splice(0, 2);

breadcrumb = '<ol class="breadcrumb">';

for (var i = 0; i < (s.length - 1); i++) {
    breadcrumb += "<li class=\"breadcrumb-item\"><a href=\"" + href.substring(0, href.indexOf("/" + s[i]) + s[i].length + 1) + "/\">" + s[i] + "</a></li>";
}
i = s.length - 1;
breadcrumb += "<li class=\"breadcrumb-item active\">" + s[i] + "</li>";

breadcrumb += "</ol>";

document.getElementById('breadcrumb').innerHTML = breadcrumb;
