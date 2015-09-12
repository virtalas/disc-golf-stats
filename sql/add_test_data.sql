INSERT INTO player (firstname, username, password) VALUES ('Teppo', 'teppoppo', 'poppopoo');

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

INSERT INTO game (courseid, gamedate, windy) VALUES (1, NOW(), true);

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
