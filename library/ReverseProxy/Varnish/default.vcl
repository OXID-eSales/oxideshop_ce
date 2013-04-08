# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
#
# Default backend definition.  Set this to point to your content
# server.
#

# import default varnish library
import std;

# include backend server configurations
include "servers_conf.vcl";

sub vcl_recv {
    call oxServerChoiseRecv;
    call oxBeginRecv;
    call oxAddSurrogateHeaders;
    call oxNonWidgetPageRecv;
    call oxGraceRecv;
    call oxNormalizeRequestRecv;
    call oxDefineDeviceTypeRecv;
    call oxDefineDefaultLangRecv;
    call oxClearCookiesByWhitelistRecv;
    call oxInvalidateRecv;
}

sub vcl_hash {
    call oxEnvKeyHash;
}

sub vcl_fetch {
    call oxInvalidateFetch;
    call oxCompressContentFetch;
    call oxDefineDeviceTypeFetch;
    call oxDefineLanguageFetch;
    call oxGraceFetch;
    call oxClearCookiesByWhitelistFetch;
    call oxCookiesFetch;
    call oxStaticContentFetch;
    call oxSkipCacheRulesFetch;
}

sub vcl_hit {
    call oxInvalidateHit;
}

sub vcl_miss {
    call oxInvalidateMiss;
    call oxBackendMiss;
}

sub vcl_pass {
    call oxInvalidatePass;
    call oxBackendPass;
}

sub vcl_deliver {
    call oxBeforeDeliver;
    call oxRemoveSurrogateHeaders;
}

/**
    Custom OXID functions
**/
sub oxBeginRecv {
    # concatenate dublicate headers to one line
    std.collect(req.http.Cache-Control);
    std.collect(req.http.Vary);
    std.collect(req.http.Pragma);
    std.collect(req.http.Cookie);

    # remove www from host
    set req.http.x-host = req.http.host;
    set req.http.x-url = req.url;
    set req.http.host = regsub(req.http.host, "^www\.", "");
    set req.http.x-cookie = req.http.Cookie;

    # admin interface not cacheable
    if (req.url ~ "/admin/" || req.url ~ "/export/genexport.txt") {
        return (pipe);
    }

    # if ESI url contains "nocookie=1" - remove cookies
    if (req.esi_level > 0 && req.url ~ "\bnocookie=1") {
        unset req.http.Cookie;
    }

    if (req.request == "POST") {
        return(pipe);
    }
}

sub oxInvalidateRecv {
    if (req.request == "PURGE") {
        if (client.ip !~ invalidators) {
            error 405 "Not allowed.";
        }

        # Band objects with url addresses whose have part of given information.
        # Call lookup if purged called from inside varnish without address to ban information.
        if (req.http.x-ban-url) {
            if (req.http.x-ban-host) {
                ban("obj.http.x-url ~ " + req.http.x-ban-url +
                    " && obj.http.x-host ~ " + req.http.x-ban-host);
            } else {
                ban("obj.http.x-url ~ " + req.http.x-ban-url);
            }
            error 200 "Purged";
        } else {
            return (lookup);
        }
    }
    if (req.request == "BAN") {
        if (client.ip !~ invalidators) {
            error 405 "Not allowed.";
        }

        # Ban objects with url address exactly same as given.
        # Host might be given as start to flush in all subshops, so we use "~" and not "==".
        ban("obj.http.x-url == " + req.http.x-ban-url +
            " && obj.http.x-host ~ " + req.http.x-ban-host);
        error 200 "Banned";
    }
    if (req.request == "REFRESH") {
        if (client.ip !~ invalidators) {
            error 405 "Not allowed.";
        }

        set req.request = "GET";
        set req.hash_always_miss = true;
    }
}

/**
    Add header for shop, to announce that Varnish is between client.
    This function should not be executed in pipe mode.
**/
sub oxAddSurrogateHeaders {
    set req.http.Surrogate-Capability = "varnish=ESI/1.0";
}

/**
    Saving detected from browser user agent device type to 
    req.http.X-UA-Device variable for later usage.
**/
sub oxDefineDeviceTypeRecv {
    if ( req.http.User-Agent ~ "iPad" ||
         req.http.User-Agent ~ "iPhone" ||
         req.http.User-Agent ~ "Android" ) {
        set req.http.X-UA-Device = "mobile";
    } else {
        set req.http.X-UA-Device = "desktop";
    }
}

/**
    Saving detected from browser language for later usage.
    We use it to backend response "Vary" header if start 
    page is called.
**/
sub oxDefineDefaultLangRecv {
    if ( req.http.Accept-Language ) {
        # Using first two letters from req.http.Accept-Language as language
        set req.http.X-UA-Language = regsub(req.http.Accept-Language, "^(\w{2}).*", "\1");
    }
}


sub oxNormalizeRequestRecv {
    # Normalize Accept-Encoding: gzip|deflate
    if (req.http.Accept-Encoding) {
        if (req.url ~ "\.(jpg|jpeg|png|gif|gz|tgz|bz2|tbz|mp3|ogg)$") {
            # No point in compressing these
            remove req.http.Accept-Encoding;
        } elsif (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elsif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            # unknown algorithm
            remove req.http.Accept-Encoding;
        }
    }

    # Some generic URL manipulation, useful for all templates that follow
    # First remove the Google Analytics added parameters, useless for our backend
    if(req.url ~ "(\?|&)(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=") {
        set req.url = regsuball(req.url, "&(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "");
        set req.url = regsuball(req.url, "\?(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "?");
        set req.url = regsub(req.url, "\?&", "?");
        set req.url = regsub(req.url, "\?$", "");
    }

    # Strip hash, server doesn't need it.
    if (req.url ~ "\#") {
        set req.url = regsub(req.url, "\#.*$", "");
    }

    # Strip a trailing ? if it exists
    if (req.url ~ "\?$") {
        set req.url = regsub(req.url, "\?$", "");
    }

    # no cookies for non HTML requests
    if (req.url ~ "\.(jpg|jpeg|png|gif|bmp|js|css|gz|tgz|bz2|tbz|mp3|ogg)$") {
        if (req.http.Cookie) {
            remove req.http.Cookie;
        }
        return(lookup);
    }
}

sub oxNonWidgetPageRecv {
    if (req.esi_level == 0) {
        if (req.url ~ "\bfnc=logout") {
            return(pass);
        }
        remove req.http.Cookie;
    }
}

sub oxGraceRecv {
    # how old data is allowed to return then the content is expired
    if (req.backend.healthy) {
        # concurent request stale data
        set req.grace = 30s;
    } else {
        # stale data time when content server is unavailable
        set req.grace = 6h;
    }
}
/**
    Clears req.http.Cookie according white list. These white cookies are passed
    to backend if needed.
**/
sub oxClearCookiesByWhitelistRecv {
    if (req.http.Cookie) {

        # concatenate dublicate headers to one line
        std.collect(req.http.Cookie);

        # removing white space from the string begining	
        set req.http.Cookie = regsub(req.http.Cookie, "^\s+", "");


        # Seting to all cookies that are in white list "@" sign before param name.
        set req.http.Cookie = regsuball(req.http.Cookie, "(^|; )(sid_key|oxid_[0-9]+_autologin|oxid_[0-9]+)=", "\1@\2=");
        # Removing all params that do not have "@" sign before param name.
        set req.http.Cookie = regsuball(req.http.Cookie, "(^|; )[^@].*?($|; )", "\2");
        # Removing @ sign before param name
        set req.http.Cookie = regsuball(req.http.Cookie, "(^|; )@", "\1");
        # Cleaning up  - empty string with "," found, cleaning it.
        set req.http.Cookie = regsuball(req.http.Cookie, "(^; )", "");

        if (req.http.Cookie == "") {
            remove req.http.Cookie;
        }
    }
}

sub oxEnvKeyHash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }

    if (req.http.x-cookie ~ "\boxenv_key=") {
        hash_data(regsub(req.http.x-cookie, ".*oxenv_key=(\w+)\b.*", "\1"));
    }
    
    set req.url = req.http.x-url;

    return (hash);
}

sub oxGraceFetch {

    # return stale data on server error (this will not work then content server is OFF)
    if (beresp.status >= 500) {
        set beresp.saintmode = 10s;
        return(restart);
    }

    # maximum time to keep in cache after TTL (should be equal to max. req.grace value)
    if (beresp.http.x-no-stale-data) {
        set beresp.grace = 0s;
    } else {
        set beresp.grace = 6h;
    }
}

sub oxInvalidateFetch {
    # add host, url to cached object for cache invalidation
    set beresp.http.x-url = req.http.x-url;
    set beresp.http.x-host = req.http.x-host;
}

sub oxSkipCacheRulesFetch {
    std.collect( beresp.http.cache-control );
    std.collect( beresp.http.pragma );

    if ( beresp.http.cache-control ~ "(no-cache|private)" ||
         beresp.http.pragma ~ "no-cache" || (beresp.status >= 300 && beresp.status < 500) ) {
        set beresp.ttl = 300s;
        return(hit_for_pass);
    }
}

sub oxStaticContentFetch {
    if (req.url ~ "\.(jpg|jpeg|png|gif|bmp|js|css|gz|tgz|bz2|tbz|mp3|ogg)$") {
        # default cache time for static content
        set beresp.ttl = 24h;
    } elseif (beresp.http.Surrogate-Control ~ "ESI") {
        set beresp.do_esi = true;
    }
}

sub oxDefineDeviceTypeFetch {
    if (req.http.X-UA-Device) {
        if (!beresp.http.Vary) { # no Vary at all
            set beresp.http.Vary = "X-UA-Device";
        } elseif (beresp.http.Vary !~ "X-UA-Device") { # add to existing Vary
            set beresp.http.Vary = beresp.http.Vary + ", X-UA-Device";
        }
        set beresp.http.X-UA-Device = req.http.X-UA-Device;
    }
}

/**
    Setting beresp.http.X-UA-Language if needed. If this is start page, backend returns 
    beresp.http.Vary, which contains "X-UA-Language" param. In this case beresp.http.X-UA-Language
    value is setted to user browser language value.
**/
sub oxDefineLanguageFetch {
    if (req.http.X-UA-Language && beresp.http.Vary ~ "X-UA-Language") {
        #setting value to previously detected language
        set beresp.http.X-UA-Language = req.http.X-UA-Language;
    }
}


/**
    Clears beresp.http.Set-Cookie according white list. If after cleaning 
    beresp.http.Set-Cookie will not be empty, requested page will not be taken
    from cache.
**/
sub oxClearCookiesByWhitelistFetch {
    if (beresp.http.Set-Cookie && req.esi_level == 0) {

        # concatenate dublicate headers to one line
        std.collect(beresp.http.Set-Cookie);

        # normalize expire entry value
        set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "(expires=.*?)(, )([^;]*?)", "\1,\3");

        set beresp.http.Set-Cookie = regsub(beresp.http.Set-Cookie, "^\s+", "");

        # Filter referer URL, change currency value, active lang
        if (req.http.referer ~ "\bcur=(\d+)" && req.url ~ "\bcur=(\d+)") {
            set req.http.referer = regsub(req.http.referer, "(\bcur=)(\d+)", "\1" + regsub(req.url, ".*\bcur=(\d+).*", "\1"));
        }
        if (req.http.referer ~ "\blang=" && req.url ~ "\blang=") {
            set req.http.referer = regsub(req.http.referer, "(\blang=)(\d+)", "\1" + regsub(req.url, ".*\blang=(\d+).*", "\1"));
        }

        # Validate Set-Cookie value, if it contains SID and it is not the same as before, do not remove it from set-cookie header
        if (beresp.http.Set-Cookie ~ "\bsid=[-\w]+\b") {
            if ( regsub(beresp.http.Set-Cookie, ".*\bsid=([-\w]+)\b.*", "\1") != regsub(req.http.x-cookie, ".*\bsid=([-\w]+)\b.*", "\1") ) {
                # replacing sid=xxx to @sid=xxx. Later all params with @ will not be removed from Set-Cookie header.
                set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "\bsid=", "@sid=");
            }
        }        

        # Validate Set-Cookie value, if it contains env_key and it is not the same as before, do not remove it from set-cookie header
        if (beresp.http.Set-Cookie ~ "\boxenv_key=\w+\b") {
            if ( regsub(beresp.http.Set-Cookie, ".*\boxenv_key=(\w*)\b.*", "\1") != regsub(req.http.x-cookie, ".*\boxenv_key=(\w*)\b.*", "\1") ) {
                # replacing oxenv_key=xxx to @oxenv_key=xxx. Later all params with @ will not be removed from Set-Cookie header.
                set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "\boxenv_key=", "@oxenv_key=");
            }
        }

        # Leave cookies which must be in user browser. 
        # Seting to all cookies that are in white list @ sign before param name.
        set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "(^|, )(displayedCookiesNotification|hideBetaNote|showlinksonce|sid_key|oxid_[0-9]+_autologin|oxid_[0-9]+)=", "\1@\2=");
        # Removing all params that do not have @ sign before param name.
        set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "(^|, )[^@].*?($|, )", "\2");
        # Removing @ sign before param name
        set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "(^|, )@", "\1");
        # Cleaning up  - empty string with "," found, cleaning it.
        set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, "(^, )", "");
        # Unset Set-Cookie if no cookies left.
        if (beresp.http.Set-Cookie == "") {
            remove beresp.http.Set-Cookie;
        } else {
            # split back dublicate headers to Set-Cookie.
            set beresp.http.Set-Cookie = regsuball(beresp.http.Set-Cookie, ", ", {"
Set-Cookie: "});
        }
    }
}

# If not esi calls and still cookies left return no cache.
sub oxCookiesFetch {
    # ESI snippets can't modify cookies
    if (beresp.http.Set-Cookie) {
        if (req.esi_level > 0) {
            # concatenate dublicate headers to one line
            std.collect(beresp.http.Set-Cookie);
            unset beresp.http.Set-Cookie;
        } else {
            # return(hit_for_pass); ??? for concurent request not to be cached.
            set beresp.ttl = 0s;
        }
    }
}

sub oxCompressContentFetch {
    if (beresp.http.content-type ~ "text") {
        set beresp.do_gzip = true;
    }
}

sub oxInvalidateHit {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged";
    }
}

sub oxInvalidateMiss {
    if (req.request == "PURGE") {
        purge;
        error 404 "Not in cache";
    }
}

sub oxBackendMiss {
    # set original cookies for backend call
    if (req.http.x-cookie) {
        set bereq.http.Cookie = req.http.x-cookie;
    }
}

sub oxInvalidatePass {
    if (req.request == "PURGE") {
        error 502 "PURGE on a passed object";
    }
}

sub oxBackendPass {
    call oxBackendMiss;
}

sub oxBeforeDeliver {
    unset resp.http.x-url;
    unset resp.http.x-host;

    if ((req.http.X-UA-Device) && (resp.http.Vary)) {
        set resp.http.Vary = regsub(resp.http.Vary, "X-UA-Device", "User-Agent");
    }
    
    # Unseting resp.http.X-UA-Language as it's not needed to pass it to
    # browser (it was used internaly by varnish)
    if (resp.http.X-UA-Language) {
        unset resp.http.X-UA-Language;
    }

    # Unseting X-UA-Language param from resp.http.Vary as there is no 
    # need to pass it to browser (it was used internaly by varnish)
    if (resp.http.Vary ~ "X-UA-Language" ) {
        set resp.http.Vary = regsub(resp.http.Vary, "(,\s+)?X-UA-Language", "");
        if (resp.http.Vary == "") {
            remove resp.http.Vary;  
        }
    }

    set resp.http.Cache-Control = "no-cache, no-store, must-revalidate, proxy-revalidate";
    set resp.http.Pragma = "no-cache";
    set resp.http.Expires = "Tue, 01 Jan 1985 00:00:00 GMT";
}

/**
    Remove surrogate headers which are only for varnish.
**/
sub oxRemoveSurrogateHeaders {
    unset resp.http.Surrogate-Control;
}

#
# Below is a commented-out copy of the default VCL logic.  If you
# redefine any of these subroutines, the built-in logic will be
# appended to your code.
# sub vcl_recv {
#     if (req.restarts == 0) {
# 	if (req.http.x-forwarded-for) {
# 	    set req.http.X-Forwarded-For =
# 		req.http.X-Forwarded-For + ", " + client.ip;
# 	} else {
# 	    set req.http.X-Forwarded-For = client.ip;
# 	}
#     }
#     if (req.request != "GET" &&
#       req.request != "HEAD" &&
#       req.request != "PUT" &&
#       req.request != "POST" &&
#       req.request != "TRACE" &&
#       req.request != "OPTIONS" &&
#       req.request != "DELETE") {
#         /* Non-RFC2616 or CONNECT which is weird. */
#         return (pipe);
#     }
#     if (req.request != "GET" && req.request != "HEAD") {
#         /* We only deal with GET and HEAD by default */
#         return (pass);
#     }
#     if (req.http.Authorization || req.http.Cookie) {
#         /* Not cacheable by default */
#         return (pass);
#     }
#     return (lookup);
# }
#
# sub vcl_pipe {
#     # Note that only the first request to the backend will have
#     # X-Forwarded-For set.  If you use X-Forwarded-For and want to
#     # have it set for all requests, make sure to have:
#     # set bereq.http.connection = "close";
#     # here.  It is not set by default as it might break some broken web
#     # applications, like IIS with NTLM authentication.
#     return (pipe);
# }
#
# sub vcl_pass {
#     return (pass);
# }
#
# sub vcl_hash {
#     hash_data(req.url);
#     if (req.http.host) {
#         hash_data(req.http.host);
#     } else {
#         hash_data(server.ip);
#     }
#     return (hash);
# }
#
# sub vcl_hit {
#     return (deliver);
# }
#
# sub vcl_miss {
#     return (fetch);
# }
#
# sub vcl_fetch {
#     if (beresp.ttl <= 0s ||
#         beresp.http.Set-Cookie ||
#         beresp.http.Vary == "*") {
# 		/*
# 		 * Mark as "Hit-For-Pass" for the next 2 minutes
# 		 */
# 		set beresp.ttl = 120 s;
# 		return (hit_for_pass);
#     }
#     return (deliver);
# }
#
# sub vcl_deliver {
#     return (deliver);
# }
#
# sub vcl_error {
#     set obj.http.Content-Type = "text/html; charset=utf-8";
#     set obj.http.Retry-After = "5";
#     synthetic {"
# <?xml version="1.0" encoding="utf-8"?>
# <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
#  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
# <html>
#   <head>
#     <title>"} + obj.status + " " + obj.response + {"</title>
#   </head>
#   <body>
#     <h1>Error "} + obj.status + " " + obj.response + {"</h1>
#     <p>"} + obj.response + {"</p>
#     <h3>Guru Meditation:</h3>
#     <p>XID: "} + req.xid + {"</p>
#     <hr>
#     <p>Varnish cache server</p>
#   </body>
# </html>
# "};
#     return (deliver);
# }
#
# sub vcl_init {
# 	return (ok);
# }
#
# sub vcl_fini {
# 	return (ok);
# }
