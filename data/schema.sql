CREATE TABLE `links` (
    `id` char(36) NOT NULL,
    `code` char(5) NOT NULL,
    `domain` varchar(36) NOT NULL,
    `url` varchar(255) NOT NULL,
    `user` char(36) NOT NULL,
    `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `links`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `code` (`code`, `domain`),
    ADD UNIQUE KEY `url` (`url`, `domain`, `user`);
COMMIT;

--

CREATE TABLE `users` (
    `id` char(36) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `user` (`username`, `password`);
COMMIT;

--

CREATE TABLE `hits` (
    `id` char(36) NOT NULL,
    `code` char(5) NOT NULL,
    `domain` varchar(255) NOT NULL,
    `ts` datetime NOT NULL,
    `ip` int UNSIGNED NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `hits`
    ADD PRIMARY KEY (`id`),
    ADD KEY `code` (`code`, `domain`);
COMMIT;