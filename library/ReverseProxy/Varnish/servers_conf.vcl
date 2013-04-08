backend server1 {
    .host = "<my_shop_hostname>";
    .port = "80";
    .probe = {
        .url = "/robots.txt";
        .interval = 5s;
        .timeout = 1s;
        .window = 5;
        .threshold = 3;
    }
}

director servers round-robin {
    {.backend = server1;}
}

/**
    Every cache invalidation IP should be appended here.
    Syntax: "x.x.x.x"; "y.y.y.y";
**/
acl invalidators {
    "127.0.0.1";
    "<my_shop_IP>";
}

sub oxServerChoiseRecv {
    set req.backend = servers;
}
