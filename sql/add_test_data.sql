-- COPY player (playerid, admin, firstname, lastname, username, password, salt) FROM stdin;
-- 1	t	Admin	\N	admin	4f.qYxkLq6Kk	4?????????\b???!?1?i??i?K?\n?l0?^?=2\b???/yT?
-- 2	f	Teppo	\N	teppo	4f.qYxkLq6Kk	4?????????\b???!?1?i??i?K?\n?l0?^?=2\b???/yT?
-- 3	f	Seppo	\N	seppo	4f.qYxkLq6Kk	4?????????\b???!?1?i??i?K?\n?l0?^?=2\b???/yT?
-- 4	f	Matti	\N	matti	4f.qYxkLq6Kk	4?????????\b???!?1?i??i?K?\n?l0?^?=2\b???/yT?
-- 5	f	Esko	\N	esko	4f.qYxkLq6Kk	4?????????\b???!?1?i??i?K?\n?l0?^?=2\b???/yT?
-- \.


INSERT INTO player (firstname, username, password) VALUES ('Teppo', 'teppo', 'salasana');
INSERT INTO player (firstname, username, password) VALUES ('Matti', 'matti', 'salasana');
INSERT INTO player (firstname, username, password) VALUES ('Seppo', 'seppo', 'salasana');
INSERT INTO player (firstname, username, password) VALUES ('Esko', 'esko', 'salasana');

INSERT INTO player (admin, firstname, username, password) VALUES (true, 'Admin', 'admin', 'salasana');

INSERT INTO course (name, city) VALUES ('Tali', 'Helsinki');

INSERT INTO hole (courseid, hole_num, par) VALUES (1, 1, 5);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 2, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 3, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 4, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 5, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 6, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 7, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 8, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 9, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 10, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 11, 4);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 12, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 13, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 14, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 15, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 16, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 17, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (1, 18, 4);

INSERT INTO course (name, city) VALUES ('Kivikko', 'Helsinki');

INSERT INTO hole (courseid, hole_num, par) VALUES (2, 1, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 2, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 3, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 4, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 5, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 6, 4);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 7, 4);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 8, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 9, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 10, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 11, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 12, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 13, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 14, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 15, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 16, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 17, 3);
INSERT INTO hole (courseid, hole_num, par) VALUES (2, 18, 3);

INSERT INTO game (courseid, gamedate, windy) VALUES (1, '2015-09-20 17:00:00', true);

INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 1, 1, 6, 1);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 2, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 3, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 4, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 5, 1, 4, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 6, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 7, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 8, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 9, 1, 5, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 10, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 11, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 12, 1, 3, 1);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 13, 1, 4, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 14, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 15, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 16, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 17, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (1, 18, 1, 5, 1);

INSERT INTO game (courseid, gamedate, rain, windy, comment) VALUES (2, NOW(), true, true, 'Väylän 6 korista oli irronnut ketju.');

INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 19, 1, 3, 1);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 20, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 21, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 22, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 23, 1, 4, 2);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 24, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 25, 1, 4, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 26, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 27, 1, 5, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 28, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 29, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 30, 1, 3, 1);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 31, 1, 4, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 32, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 33, 1, 2, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 34, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 35, 1, 3, 0);
INSERT INTO score (gameid, holeid, playerid, stroke, ob) VALUES (2, 36, 1, 4, 1);
