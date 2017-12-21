--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: plpgsql_call_handler(); Type: FUNCTION; Schema: public; Owner: lasvirt
--

CREATE FUNCTION plpgsql_call_handler() RETURNS language_handler
    LANGUAGE c
    AS '$libdir/plpgsql', 'plpgsql_call_handler';


ALTER FUNCTION public.plpgsql_call_handler() OWNER TO lasvirt;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: auth_group; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_group (
    id integer NOT NULL,
    name character varying(80) NOT NULL
);


ALTER TABLE auth_group OWNER TO lasvirt;

--
-- Name: auth_group_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_group_id_seq OWNER TO lasvirt;

--
-- Name: auth_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_group_id_seq OWNED BY auth_group.id;


--
-- Name: auth_group_permissions; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_group_permissions (
    id integer NOT NULL,
    group_id integer NOT NULL,
    permission_id integer NOT NULL
);


ALTER TABLE auth_group_permissions OWNER TO lasvirt;

--
-- Name: auth_group_permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_group_permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_group_permissions_id_seq OWNER TO lasvirt;

--
-- Name: auth_group_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_group_permissions_id_seq OWNED BY auth_group_permissions.id;


--
-- Name: auth_permission; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_permission (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    content_type_id integer NOT NULL,
    codename character varying(100) NOT NULL
);


ALTER TABLE auth_permission OWNER TO lasvirt;

--
-- Name: auth_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_permission_id_seq OWNER TO lasvirt;

--
-- Name: auth_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_permission_id_seq OWNED BY auth_permission.id;


--
-- Name: auth_user; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_user (
    id integer NOT NULL,
    password character varying(128) NOT NULL,
    last_login timestamp with time zone,
    is_superuser boolean NOT NULL,
    username character varying(30) NOT NULL,
    first_name character varying(30) NOT NULL,
    last_name character varying(30) NOT NULL,
    email character varying(254) NOT NULL,
    is_staff boolean NOT NULL,
    is_active boolean NOT NULL,
    date_joined timestamp with time zone NOT NULL
);


ALTER TABLE auth_user OWNER TO lasvirt;

--
-- Name: auth_user_groups; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_user_groups (
    id integer NOT NULL,
    user_id integer NOT NULL,
    group_id integer NOT NULL
);


ALTER TABLE auth_user_groups OWNER TO lasvirt;

--
-- Name: auth_user_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_user_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_user_groups_id_seq OWNER TO lasvirt;

--
-- Name: auth_user_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_user_groups_id_seq OWNED BY auth_user_groups.id;


--
-- Name: auth_user_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_user_id_seq OWNER TO lasvirt;

--
-- Name: auth_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_user_id_seq OWNED BY auth_user.id;


--
-- Name: auth_user_user_permissions; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE auth_user_user_permissions (
    id integer NOT NULL,
    user_id integer NOT NULL,
    permission_id integer NOT NULL
);


ALTER TABLE auth_user_user_permissions OWNER TO lasvirt;

--
-- Name: auth_user_user_permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE auth_user_user_permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE auth_user_user_permissions_id_seq OWNER TO lasvirt;

--
-- Name: auth_user_user_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE auth_user_user_permissions_id_seq OWNED BY auth_user_user_permissions.id;


--
-- Name: card_minutes; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE card_minutes (
    id integer NOT NULL,
    minutes_per_day integer NOT NULL,
    user_id integer NOT NULL,
    end_date timestamp with time zone,
    start_date timestamp with time zone NOT NULL
);


ALTER TABLE card_minutes OWNER TO lasvirt;

--
-- Name: card_minutes_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE card_minutes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE card_minutes_id_seq OWNER TO lasvirt;

--
-- Name: card_minutes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE card_minutes_id_seq OWNED BY card_minutes.id;


--
-- Name: card_project; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE card_project (
    id integer NOT NULL,
    name character varying(200) NOT NULL,
    start_date timestamp with time zone NOT NULL,
    end_date timestamp with time zone,
    user_id integer NOT NULL
);


ALTER TABLE card_project OWNER TO lasvirt;

--
-- Name: card_project_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE card_project_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE card_project_id_seq OWNER TO lasvirt;

--
-- Name: card_project_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE card_project_id_seq OWNED BY card_project.id;


--
-- Name: card_work; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE card_work (
    id integer NOT NULL,
    start_time timestamp with time zone NOT NULL,
    end_time timestamp with time zone,
    project_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE card_work OWNER TO lasvirt;

--
-- Name: card_word_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE card_word_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE card_word_id_seq OWNER TO lasvirt;

--
-- Name: card_word_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE card_word_id_seq OWNED BY card_work.id;


--
-- Name: contest; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE contest (
    contestid integer NOT NULL,
    creator integer,
    name character varying(300),
    number_of_games smallint NOT NULL
);


ALTER TABLE contest OWNER TO lasvirt;

--
-- Name: contest_contestid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE contest_contestid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE contest_contestid_seq OWNER TO lasvirt;

--
-- Name: contest_contestid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE contest_contestid_seq OWNED BY contest.contestid;


--
-- Name: course; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE course (
    courseid integer NOT NULL,
    name character varying(50) NOT NULL,
    city character varying(50),
    map character varying(300)
);


ALTER TABLE course OWNER TO lasvirt;

--
-- Name: course_courseid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE course_courseid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE course_courseid_seq OWNER TO lasvirt;

--
-- Name: course_courseid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE course_courseid_seq OWNED BY course.courseid;


--
-- Name: django_admin_log; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE django_admin_log (
    id integer NOT NULL,
    action_time timestamp with time zone NOT NULL,
    object_id text,
    object_repr character varying(200) NOT NULL,
    action_flag smallint NOT NULL,
    change_message text NOT NULL,
    content_type_id integer,
    user_id integer NOT NULL,
    CONSTRAINT django_admin_log_action_flag_check CHECK ((action_flag >= 0))
);


ALTER TABLE django_admin_log OWNER TO lasvirt;

--
-- Name: django_admin_log_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE django_admin_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE django_admin_log_id_seq OWNER TO lasvirt;

--
-- Name: django_admin_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE django_admin_log_id_seq OWNED BY django_admin_log.id;


--
-- Name: django_content_type; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE django_content_type (
    id integer NOT NULL,
    app_label character varying(100) NOT NULL,
    model character varying(100) NOT NULL
);


ALTER TABLE django_content_type OWNER TO lasvirt;

--
-- Name: django_content_type_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE django_content_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE django_content_type_id_seq OWNER TO lasvirt;

--
-- Name: django_content_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE django_content_type_id_seq OWNED BY django_content_type.id;


--
-- Name: django_migrations; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE django_migrations (
    id integer NOT NULL,
    app character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    applied timestamp with time zone NOT NULL
);


ALTER TABLE django_migrations OWNER TO lasvirt;

--
-- Name: django_migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE django_migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE django_migrations_id_seq OWNER TO lasvirt;

--
-- Name: django_migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE django_migrations_id_seq OWNED BY django_migrations.id;


--
-- Name: django_session; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE django_session (
    session_key character varying(40) NOT NULL,
    session_data text NOT NULL,
    expire_date timestamp with time zone NOT NULL
);


ALTER TABLE django_session OWNER TO lasvirt;

--
-- Name: game; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE game (
    gameid integer NOT NULL,
    courseid integer,
    creator integer,
    contestid integer,
    gamedate timestamp without time zone,
    comment character varying(1000),
    rain boolean DEFAULT false,
    wet_no_rain boolean DEFAULT false,
    windy boolean DEFAULT false,
    variant boolean DEFAULT false,
    dark boolean DEFAULT false,
    led boolean DEFAULT false,
    snow boolean DEFAULT false,
    doubles boolean DEFAULT false,
    temp real
);


ALTER TABLE game OWNER TO lasvirt;

--
-- Name: game_gameid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE game_gameid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE game_gameid_seq OWNER TO lasvirt;

--
-- Name: game_gameid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE game_gameid_seq OWNED BY game.gameid;


--
-- Name: hole; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE hole (
    holeid integer NOT NULL,
    courseid integer,
    hole_num smallint NOT NULL,
    par smallint NOT NULL
);


ALTER TABLE hole OWNER TO lasvirt;

--
-- Name: hole_holeid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE hole_holeid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE hole_holeid_seq OWNER TO lasvirt;

--
-- Name: hole_holeid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE hole_holeid_seq OWNED BY hole.holeid;


--
-- Name: player; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE player (
    playerid integer NOT NULL,
    admin boolean DEFAULT false,
    firstname character varying(50) NOT NULL,
    lastname character varying(50),
    username character varying(50) NOT NULL,
    password character varying(100) NOT NULL,
    salt character varying(100)
);


ALTER TABLE player OWNER TO lasvirt;

--
-- Name: player_playerid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE player_playerid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE player_playerid_seq OWNER TO lasvirt;

--
-- Name: player_playerid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE player_playerid_seq OWNED BY player.playerid;


--
-- Name: score; Type: TABLE; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE TABLE score (
    scoreid integer NOT NULL,
    gameid integer,
    holeid integer,
    playerid integer,
    stroke smallint NOT NULL,
    ob smallint DEFAULT 0,
    legal boolean DEFAULT true
);


ALTER TABLE score OWNER TO lasvirt;

--
-- Name: score_scoreid_seq; Type: SEQUENCE; Schema: public; Owner: lasvirt
--

CREATE SEQUENCE score_scoreid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE score_scoreid_seq OWNER TO lasvirt;

--
-- Name: score_scoreid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: lasvirt
--

ALTER SEQUENCE score_scoreid_seq OWNED BY score.scoreid;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_group ALTER COLUMN id SET DEFAULT nextval('auth_group_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_group_permissions ALTER COLUMN id SET DEFAULT nextval('auth_group_permissions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_permission ALTER COLUMN id SET DEFAULT nextval('auth_permission_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user ALTER COLUMN id SET DEFAULT nextval('auth_user_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_groups ALTER COLUMN id SET DEFAULT nextval('auth_user_groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_user_permissions ALTER COLUMN id SET DEFAULT nextval('auth_user_user_permissions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_minutes ALTER COLUMN id SET DEFAULT nextval('card_minutes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_project ALTER COLUMN id SET DEFAULT nextval('card_project_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_work ALTER COLUMN id SET DEFAULT nextval('card_word_id_seq'::regclass);


--
-- Name: contestid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY contest ALTER COLUMN contestid SET DEFAULT nextval('contest_contestid_seq'::regclass);


--
-- Name: courseid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY course ALTER COLUMN courseid SET DEFAULT nextval('course_courseid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY django_admin_log ALTER COLUMN id SET DEFAULT nextval('django_admin_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY django_content_type ALTER COLUMN id SET DEFAULT nextval('django_content_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY django_migrations ALTER COLUMN id SET DEFAULT nextval('django_migrations_id_seq'::regclass);


--
-- Name: gameid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY game ALTER COLUMN gameid SET DEFAULT nextval('game_gameid_seq'::regclass);


--
-- Name: holeid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY hole ALTER COLUMN holeid SET DEFAULT nextval('hole_holeid_seq'::regclass);


--
-- Name: playerid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY player ALTER COLUMN playerid SET DEFAULT nextval('player_playerid_seq'::regclass);


--
-- Name: scoreid; Type: DEFAULT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY score ALTER COLUMN scoreid SET DEFAULT nextval('score_scoreid_seq'::regclass);


--
-- Data for Name: auth_group; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_group (id, name) FROM stdin;
\.


--
-- Name: auth_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_group_id_seq', 1, false);


--
-- Data for Name: auth_group_permissions; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_group_permissions (id, group_id, permission_id) FROM stdin;
\.


--
-- Name: auth_group_permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_group_permissions_id_seq', 1, false);


--
-- Data for Name: auth_permission; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_permission (id, name, content_type_id, codename) FROM stdin;
1	Can add log entry	1	add_logentry
2	Can change log entry	1	change_logentry
3	Can delete log entry	1	delete_logentry
4	Can add permission	2	add_permission
5	Can change permission	2	change_permission
6	Can delete permission	2	delete_permission
7	Can add group	3	add_group
8	Can change group	3	change_group
9	Can delete group	3	delete_group
10	Can add user	4	add_user
11	Can change user	4	change_user
12	Can delete user	4	delete_user
13	Can add content type	5	add_contenttype
14	Can change content type	5	change_contenttype
15	Can delete content type	5	delete_contenttype
16	Can add session	6	add_session
17	Can change session	6	change_session
18	Can delete session	6	delete_session
22	Can add project	8	add_project
23	Can change project	8	change_project
24	Can delete project	8	delete_project
28	Can add work	10	add_work
29	Can change work	10	change_work
30	Can delete work	10	delete_work
31	Can add minutes	11	add_minutes
32	Can change minutes	11	change_minutes
33	Can delete minutes	11	delete_minutes
\.


--
-- Name: auth_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_permission_id_seq', 33, true);


--
-- Data for Name: auth_user; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_user (id, password, last_login, is_superuser, username, first_name, last_name, email, is_staff, is_active, date_joined) FROM stdin;
8	pbkdf2_sha256$24000$2Tw3T364YA4m$7lpL55XClEQqOfwu6V7DhzfuKdDJ+YejUGizKLsVV5E=	2016-06-08 19:11:43.35166+03	f	seppo@email.com	Seppo		seppo@email.com	f	t	2016-05-28 15:57:30.974438+03
\.


--
-- Data for Name: auth_user_groups; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_user_groups (id, user_id, group_id) FROM stdin;
\.


--
-- Name: auth_user_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_user_groups_id_seq', 1, false);


--
-- Name: auth_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_user_id_seq', 8, true);


--
-- Data for Name: auth_user_user_permissions; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY auth_user_user_permissions (id, user_id, permission_id) FROM stdin;
\.


--
-- Name: auth_user_user_permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('auth_user_user_permissions_id_seq', 1, false);


--
-- Data for Name: card_minutes; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY card_minutes (id, minutes_per_day, user_id, end_date, start_date) FROM stdin;
7	300	8	2016-05-01 00:00:00+03	2016-04-12 00:00:00+03
6	480	8	2016-06-12 00:00:00+03	2016-06-12 00:00:00+03
8	444	8	\N	2016-06-12 00:00:00+03
\.


--
-- Name: card_minutes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('card_minutes_id_seq', 8, true);


--
-- Data for Name: card_project; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY card_project (id, name, start_date, end_date, user_id) FROM stdin;
11	dhfh	2016-05-28 16:56:37.495614+03	\N	8
12	hfthgrdgs	2016-05-28 16:56:40.328617+03	\N	8
13	Projekti jolla on pitkä nimi joo	2016-06-08 21:33:02.104542+03	\N	8
\.


--
-- Name: card_project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('card_project_id_seq', 13, true);


--
-- Name: card_word_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('card_word_id_seq', 70, true);


--
-- Data for Name: card_work; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY card_work (id, start_time, end_time, project_id, user_id) FROM stdin;
67	2016-06-12 14:46:44.386276+03	2016-06-12 14:46:46.558069+03	13	8
68	2016-06-12 14:46:01.117938+03	2016-06-12 14:53:24.339188+03	13	8
69	2016-06-12 14:53:28.36001+03	2016-06-12 14:53:36.16425+03	12	8
70	2016-04-13 15:00:00+03	2016-04-13 16:00:00+03	11	8
\.


--
-- Data for Name: contest; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY contest (contestid, creator, name, number_of_games) FROM stdin;
\.


--
-- Name: contest_contestid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('contest_contestid_seq', 1, false);


--
-- Data for Name: course; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY course (courseid, name, city, map) FROM stdin;
2	Kivikko	Helsinki	\N
1	Tali	Helsinki	
\.


--
-- Name: course_courseid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('course_courseid_seq', 2, true);


--
-- Data for Name: django_admin_log; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY django_admin_log (id, action_time, object_id, object_repr, action_flag, change_message, content_type_id, user_id) FROM stdin;
\.


--
-- Name: django_admin_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('django_admin_log_id_seq', 1, false);


--
-- Data for Name: django_content_type; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY django_content_type (id, app_label, model) FROM stdin;
1	admin	logentry
2	auth	permission
3	auth	group
4	auth	user
5	contenttypes	contenttype
6	sessions	session
8	card	project
10	card	work
11	card	minutes
\.


--
-- Name: django_content_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('django_content_type_id_seq', 11, true);


--
-- Data for Name: django_migrations; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY django_migrations (id, app, name, applied) FROM stdin;
1	contenttypes	0001_initial	2016-05-18 18:42:38.190732+03
2	auth	0001_initial	2016-05-18 18:42:38.426402+03
3	admin	0001_initial	2016-05-18 18:42:38.506217+03
4	admin	0002_logentry_remove_auto_add	2016-05-18 18:42:38.553924+03
5	contenttypes	0002_remove_content_type_name	2016-05-18 18:42:38.596698+03
6	auth	0002_alter_permission_name_max_length	2016-05-18 18:42:38.614309+03
7	auth	0003_alter_user_email_max_length	2016-05-18 18:42:38.631095+03
8	auth	0004_alter_user_username_opts	2016-05-18 18:42:38.651551+03
9	auth	0005_alter_user_last_login_null	2016-05-18 18:42:38.667384+03
10	auth	0006_require_contenttypes_0002	2016-05-18 18:42:38.670144+03
11	auth	0007_alter_validators_add_error_messages	2016-05-18 18:42:38.684952+03
12	sessions	0001_initial	2016-05-18 18:42:38.736481+03
13	card	0001_initial	2016-05-18 19:15:04.28922+03
14	card	0002_auto_20160518_1615	2016-05-18 19:15:55.911644+03
15	card	0003_auto_20160519_1540	2016-05-19 18:40:23.239179+03
16	card	0004_auto_20160519_1547	2016-05-19 18:48:03.272317+03
17	card	0005_auto_20160519_1548	2016-05-19 18:48:44.244079+03
18	card	0006_auto_20160525_1630	2016-05-25 16:30:32.569395+03
19	card	0007_auto_20160612_1437	2016-06-12 14:37:14.853564+03
\.


--
-- Name: django_migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('django_migrations_id_seq', 19, true);


--
-- Data for Name: django_session; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY django_session (session_key, session_data, expire_date) FROM stdin;
wns8zxc43d6z6zeji4c849wejt7ej0bj	MTBmYzE4NmZhMDU1YmJiYTQ2MmY3YjQxMGU2NGEwNjdkMWMzMDgxZTp7Il9hdXRoX3VzZXJfaGFzaCI6IjRhMTI3OTQyZTY5MTlhNGM3ZDljODgwN2Q4Mzg2NGM4MzJlZDJkYWQiLCJfYXV0aF91c2VyX2JhY2tlbmQiOiJkamFuZ28uY29udHJpYi5hdXRoLmJhY2tlbmRzLk1vZGVsQmFja2VuZCIsIl9hdXRoX3VzZXJfaWQiOiI4In0=	2016-06-22 19:11:43.359812+03
\.


--
-- Data for Name: game; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY game (gameid, courseid, creator, contestid, gamedate, comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles, temp) FROM stdin;
1	1	1	\N	2017-07-01 12:41:00		f	f	t	f	f	f	f	f	17
2	1	1	\N	2017-07-03 20:10:00	Kierrokseen meni 3,5 tuntia.	f	f	t	f	f	f	f	f	16
3	1	1	\N	2017-07-26 21:37:00		f	f	t	f	f	f	f	f	19
4	1	1	\N	2017-07-28 18:00:00		f	t	f	f	f	f	f	f	20
\.


--
-- Name: game_gameid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('game_gameid_seq', 4, true);


--
-- Data for Name: hole; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY hole (holeid, courseid, hole_num, par) FROM stdin;
19	2	1	3
20	2	2	3
21	2	3	3
22	2	4	3
23	2	5	3
24	2	6	4
25	2	7	4
26	2	8	3
27	2	9	3
28	2	10	3
29	2	11	3
30	2	12	3
31	2	13	3
32	2	14	3
33	2	15	3
34	2	16	3
35	2	17	3
36	2	18	3
1	1	1	5
2	1	2	3
3	1	3	3
4	1	4	3
5	1	5	3
6	1	6	3
7	1	7	3
8	1	8	3
9	1	9	3
10	1	10	4
11	1	11	3
12	1	12	3
13	1	13	3
14	1	14	3
15	1	15	3
16	1	16	3
17	1	17	4
18	1	18	3
\.


--
-- Name: hole_holeid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('hole_holeid_seq', 36, true);


--
-- Data for Name: player; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY player (playerid, admin, firstname, lastname, username, password, salt) FROM stdin;
1	t	Admin	\N	admin	?F574t2qyQn.	?}???hy(? uٟ\nI?/??$?~??H?&?f??յh???:g30?\r3
2	f	Matti	\N	matti	`WHXU5ZRx5uek	`Wr??gP%9?????X?O-d??|?;'\\K赧TY??pS???
3	f	Teppo	\N	teppo	2ekdO5Aq0.9pY	2e3?????sL]Gg+??\t??yH?gMI?\t?U?w?=qX4???VPԐ?N?
4	f	Seppo	\N	seppo	??M3cXkypTLWE	???%??????9?׫?C????????{?x?X??@?u?LM?????$
5	f	Esko	\N	esko	T?VR7D96sdKwg	T?k?L?Mm?????\v???mɣ??F????JHw?S??G?I䡄b}?
\.


--
-- Name: player_playerid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('player_playerid_seq', 5, true);


--
-- Data for Name: score; Type: TABLE DATA; Schema: public; Owner: lasvirt
--

COPY score (scoreid, gameid, holeid, playerid, stroke, ob, legal) FROM stdin;
1	1	1	1	6	0	t
2	1	2	1	3	1	t
3	1	3	1	4	0	t
4	1	4	1	4	0	t
5	1	5	1	2	0	t
6	1	6	1	3	0	t
7	1	7	1	3	0	t
8	1	8	1	5	0	t
9	1	9	1	2	0	t
10	1	10	1	4	0	t
11	1	11	1	4	0	t
12	1	12	1	3	0	t
13	1	13	1	3	0	t
14	1	14	1	3	0	t
15	1	15	1	4	0	t
16	1	16	1	2	1	t
17	1	17	1	5	1	t
18	1	18	1	4	1	t
19	2	1	3	5	0	t
20	2	2	3	4	0	t
21	2	3	3	3	0	t
22	2	4	3	3	0	t
23	2	5	3	4	0	t
24	2	6	3	4	0	t
25	2	7	3	3	0	t
26	2	8	3	3	0	t
27	2	9	3	3	0	t
28	2	10	3	4	0	t
29	2	11	3	3	0	t
30	2	12	3	3	0	t
31	2	13	3	4	1	t
32	2	14	3	5	0	t
33	2	15	3	2	0	t
34	2	16	3	3	0	t
35	2	17	3	4	1	t
36	2	18	3	3	1	t
37	2	1	1	7	1	t
38	2	2	1	2	0	t
39	2	3	1	3	0	t
40	2	4	1	3	0	t
41	2	5	1	3	0	t
42	2	6	1	3	1	t
43	2	7	1	3	0	t
44	2	8	1	3	0	t
45	2	9	1	3	0	t
46	2	10	1	5	0	t
47	2	11	1	4	0	t
48	2	12	1	3	0	t
49	2	13	1	3	1	t
50	2	14	1	3	0	t
51	2	15	1	3	0	t
52	2	16	1	4	0	t
53	2	17	1	4	2	t
54	2	18	1	4	1	t
55	2	1	4	5	0	t
56	2	2	4	4	0	t
57	2	3	4	2	0	t
58	2	4	4	4	0	t
59	2	5	4	4	0	t
60	2	6	4	4	0	t
61	2	7	4	4	0	t
62	2	8	4	5	0	t
63	2	9	4	3	0	t
64	2	10	4	5	0	t
65	2	11	4	5	2	t
66	2	12	4	3	0	t
67	2	13	4	3	0	t
68	2	14	4	4	0	t
69	2	15	4	5	0	t
70	2	16	4	4	0	t
71	2	17	4	4	0	t
72	2	18	4	2	1	t
73	2	1	5	6	0	t
74	2	2	5	3	0	t
75	2	3	5	4	0	t
76	2	4	5	4	0	t
77	2	5	5	3	0	t
78	2	6	5	4	0	t
79	2	7	5	5	0	t
80	2	8	5	5	0	t
81	2	9	5	2	0	t
82	2	10	5	7	0	t
83	2	11	5	4	1	t
84	2	12	5	4	0	t
85	2	13	5	6	2	t
86	2	14	5	4	0	t
87	2	15	5	6	0	t
88	2	16	5	3	0	t
89	2	17	5	5	2	t
90	2	18	5	3	0	t
91	3	1	3	6	0	t
92	3	2	3	3	0	t
93	3	3	3	4	0	t
94	3	4	3	3	0	t
95	3	5	3	3	0	t
96	3	6	3	2	0	t
97	3	7	3	4	1	t
98	3	8	3	4	0	t
99	3	9	3	2	0	t
100	3	10	3	5	0	t
101	3	11	3	2	0	t
102	3	12	3	4	1	t
103	3	13	3	3	0	t
104	3	14	3	3	0	t
105	3	15	3	3	0	t
106	3	16	3	2	0	t
107	3	17	3	4	0	t
108	3	18	3	3	0	t
109	3	1	1	5	1	t
110	3	2	1	5	1	t
111	3	3	1	3	0	t
112	3	4	1	3	0	t
113	3	5	1	5	0	t
114	3	6	1	3	0	t
115	3	7	1	4	0	t
116	3	8	1	3	0	t
117	3	9	1	3	0	t
118	3	10	1	6	0	t
119	3	11	1	4	1	t
120	3	12	1	3	0	t
121	3	13	1	3	1	t
122	3	14	1	3	0	t
123	3	15	1	2	0	t
124	3	16	1	3	0	t
125	3	17	1	4	0	t
126	3	18	1	3	1	t
127	3	1	5	6	0	t
128	3	2	5	3	0	t
129	3	3	5	3	0	t
130	3	4	5	4	0	t
131	3	5	5	4	0	t
132	3	6	5	4	0	t
133	3	7	5	5	0	t
134	3	8	5	5	0	t
135	3	9	5	5	0	t
136	3	10	5	6	0	t
137	3	11	5	3	0	t
138	3	12	5	4	0	t
139	3	13	5	4	0	t
140	3	14	5	5	1	t
141	3	15	5	3	0	t
142	3	16	5	4	0	t
143	3	17	5	6	1	t
144	3	18	5	3	0	t
145	3	1	4	7	1	t
146	3	2	4	3	0	t
147	3	3	4	4	0	t
148	3	4	4	4	0	t
149	3	5	4	5	0	t
150	3	6	4	4	0	t
151	3	7	4	4	0	t
152	3	8	4	4	0	t
153	3	9	4	3	0	t
154	3	10	4	5	0	t
155	3	11	4	4	0	t
156	3	12	4	4	0	t
157	3	13	4	4	0	t
158	3	14	4	5	0	t
159	3	15	4	4	0	t
160	3	16	4	3	0	t
161	3	17	4	7	1	t
162	3	18	4	4	0	t
163	4	1	1	5	0	t
164	4	2	1	3	0	t
165	4	3	1	4	0	t
166	4	4	1	3	0	t
167	4	5	1	3	0	t
168	4	6	1	4	0	t
169	4	7	1	4	0	t
170	4	8	1	4	0	t
171	4	9	1	3	0	t
172	4	10	1	3	0	t
173	4	11	1	3	0	t
174	4	12	1	3	0	t
175	4	13	1	3	1	t
176	4	14	1	4	1	t
177	4	15	1	3	0	t
178	4	16	1	3	0	t
179	4	17	1	4	0	t
180	4	18	1	3	1	t
181	4	1	3	6	0	t
182	4	2	3	3	0	t
183	4	3	3	4	0	t
184	4	4	3	4	0	t
185	4	5	3	3	1	t
186	4	6	3	4	0	t
187	4	7	3	4	0	t
188	4	8	3	6	0	t
189	4	9	3	3	0	t
190	4	10	3	6	0	t
191	4	11	3	3	0	t
192	4	12	3	5	0	t
193	4	13	3	4	1	t
194	4	14	3	5	1	t
195	4	15	3	3	0	t
196	4	16	3	3	0	t
197	4	17	3	6	0	t
198	4	18	3	4	0	t
199	4	1	5	8	0	t
200	4	2	5	4	0	t
201	4	3	5	4	0	t
202	4	4	5	3	0	t
203	4	5	5	4	0	t
204	4	6	5	5	0	t
205	4	7	5	5	0	t
206	4	8	5	7	0	t
207	4	9	5	4	0	t
208	4	10	5	5	0	t
209	4	11	5	4	0	t
210	4	12	5	5	0	t
211	4	13	5	5	0	t
212	4	14	5	6	2	t
213	4	15	5	4	0	t
214	4	16	5	3	0	t
215	4	17	5	9	2	t
216	4	18	5	6	2	t
\.


--
-- Name: score_scoreid_seq; Type: SEQUENCE SET; Schema: public; Owner: lasvirt
--

SELECT pg_catalog.setval('score_scoreid_seq', 216, true);


--
-- Name: auth_group_name_key; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_group
    ADD CONSTRAINT auth_group_name_key UNIQUE (name);


--
-- Name: auth_group_permissions_group_id_0cd325b0_uniq; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_group_permissions
    ADD CONSTRAINT auth_group_permissions_group_id_0cd325b0_uniq UNIQUE (group_id, permission_id);


--
-- Name: auth_group_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_group_permissions
    ADD CONSTRAINT auth_group_permissions_pkey PRIMARY KEY (id);


--
-- Name: auth_group_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_group
    ADD CONSTRAINT auth_group_pkey PRIMARY KEY (id);


--
-- Name: auth_permission_content_type_id_01ab375a_uniq; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_permission
    ADD CONSTRAINT auth_permission_content_type_id_01ab375a_uniq UNIQUE (content_type_id, codename);


--
-- Name: auth_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_permission
    ADD CONSTRAINT auth_permission_pkey PRIMARY KEY (id);


--
-- Name: auth_user_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user_groups
    ADD CONSTRAINT auth_user_groups_pkey PRIMARY KEY (id);


--
-- Name: auth_user_groups_user_id_94350c0c_uniq; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user_groups
    ADD CONSTRAINT auth_user_groups_user_id_94350c0c_uniq UNIQUE (user_id, group_id);


--
-- Name: auth_user_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_pkey PRIMARY KEY (id);


--
-- Name: auth_user_user_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user_user_permissions
    ADD CONSTRAINT auth_user_user_permissions_pkey PRIMARY KEY (id);


--
-- Name: auth_user_user_permissions_user_id_14a6b632_uniq; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user_user_permissions
    ADD CONSTRAINT auth_user_user_permissions_user_id_14a6b632_uniq UNIQUE (user_id, permission_id);


--
-- Name: auth_user_username_key; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_username_key UNIQUE (username);


--
-- Name: card_minutes_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY card_minutes
    ADD CONSTRAINT card_minutes_pkey PRIMARY KEY (id);


--
-- Name: card_project_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY card_project
    ADD CONSTRAINT card_project_pkey PRIMARY KEY (id);


--
-- Name: card_word_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY card_work
    ADD CONSTRAINT card_word_pkey PRIMARY KEY (id);


--
-- Name: contest_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY contest
    ADD CONSTRAINT contest_pkey PRIMARY KEY (contestid);


--
-- Name: course_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY course
    ADD CONSTRAINT course_pkey PRIMARY KEY (courseid);


--
-- Name: django_admin_log_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY django_admin_log
    ADD CONSTRAINT django_admin_log_pkey PRIMARY KEY (id);


--
-- Name: django_content_type_app_label_76bd3d3b_uniq; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY django_content_type
    ADD CONSTRAINT django_content_type_app_label_76bd3d3b_uniq UNIQUE (app_label, model);


--
-- Name: django_content_type_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY django_content_type
    ADD CONSTRAINT django_content_type_pkey PRIMARY KEY (id);


--
-- Name: django_migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY django_migrations
    ADD CONSTRAINT django_migrations_pkey PRIMARY KEY (id);


--
-- Name: django_session_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY django_session
    ADD CONSTRAINT django_session_pkey PRIMARY KEY (session_key);


--
-- Name: game_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY game
    ADD CONSTRAINT game_pkey PRIMARY KEY (gameid);


--
-- Name: hole_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY hole
    ADD CONSTRAINT hole_pkey PRIMARY KEY (holeid);


--
-- Name: player_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY player
    ADD CONSTRAINT player_pkey PRIMARY KEY (playerid);


--
-- Name: player_username_key; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY player
    ADD CONSTRAINT player_username_key UNIQUE (username);


--
-- Name: score_pkey; Type: CONSTRAINT; Schema: public; Owner: lasvirt; Tablespace: 
--

ALTER TABLE ONLY score
    ADD CONSTRAINT score_pkey PRIMARY KEY (scoreid);


--
-- Name: auth_group_name_a6ea08ec_like; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_group_name_a6ea08ec_like ON auth_group USING btree (name varchar_pattern_ops);


--
-- Name: auth_group_permissions_0e939a4f; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_group_permissions_0e939a4f ON auth_group_permissions USING btree (group_id);


--
-- Name: auth_group_permissions_8373b171; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_group_permissions_8373b171 ON auth_group_permissions USING btree (permission_id);


--
-- Name: auth_permission_417f1b1c; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_permission_417f1b1c ON auth_permission USING btree (content_type_id);


--
-- Name: auth_user_groups_0e939a4f; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_user_groups_0e939a4f ON auth_user_groups USING btree (group_id);


--
-- Name: auth_user_groups_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_user_groups_e8701ad4 ON auth_user_groups USING btree (user_id);


--
-- Name: auth_user_user_permissions_8373b171; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_user_user_permissions_8373b171 ON auth_user_user_permissions USING btree (permission_id);


--
-- Name: auth_user_user_permissions_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_user_user_permissions_e8701ad4 ON auth_user_user_permissions USING btree (user_id);


--
-- Name: auth_user_username_6821ab7c_like; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX auth_user_username_6821ab7c_like ON auth_user USING btree (username varchar_pattern_ops);


--
-- Name: card_minutes_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX card_minutes_e8701ad4 ON card_minutes USING btree (user_id);


--
-- Name: card_project_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX card_project_e8701ad4 ON card_project USING btree (user_id);


--
-- Name: card_word_b098ad43; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX card_word_b098ad43 ON card_work USING btree (project_id);


--
-- Name: card_word_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX card_word_e8701ad4 ON card_work USING btree (user_id);


--
-- Name: django_admin_log_417f1b1c; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX django_admin_log_417f1b1c ON django_admin_log USING btree (content_type_id);


--
-- Name: django_admin_log_e8701ad4; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX django_admin_log_e8701ad4 ON django_admin_log USING btree (user_id);


--
-- Name: django_session_de54fa62; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX django_session_de54fa62 ON django_session USING btree (expire_date);


--
-- Name: django_session_session_key_c0390e0f_like; Type: INDEX; Schema: public; Owner: lasvirt; Tablespace: 
--

CREATE INDEX django_session_session_key_c0390e0f_like ON django_session USING btree (session_key varchar_pattern_ops);


--
-- Name: auth_group_permiss_permission_id_84c5c92e_fk_auth_permission_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_group_permissions
    ADD CONSTRAINT auth_group_permiss_permission_id_84c5c92e_fk_auth_permission_id FOREIGN KEY (permission_id) REFERENCES auth_permission(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_group_permissions_group_id_b120cbf9_fk_auth_group_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_group_permissions
    ADD CONSTRAINT auth_group_permissions_group_id_b120cbf9_fk_auth_group_id FOREIGN KEY (group_id) REFERENCES auth_group(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_permiss_content_type_id_2f476e4b_fk_django_content_type_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_permission
    ADD CONSTRAINT auth_permiss_content_type_id_2f476e4b_fk_django_content_type_id FOREIGN KEY (content_type_id) REFERENCES django_content_type(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_user_groups_group_id_97559544_fk_auth_group_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_groups
    ADD CONSTRAINT auth_user_groups_group_id_97559544_fk_auth_group_id FOREIGN KEY (group_id) REFERENCES auth_group(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_user_groups_user_id_6a12ed8b_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_groups
    ADD CONSTRAINT auth_user_groups_user_id_6a12ed8b_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_user_user_per_permission_id_1fbb5f2c_fk_auth_permission_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_user_permissions
    ADD CONSTRAINT auth_user_user_per_permission_id_1fbb5f2c_fk_auth_permission_id FOREIGN KEY (permission_id) REFERENCES auth_permission(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY auth_user_user_permissions
    ADD CONSTRAINT auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: card_minutes_user_id_12e511cc_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_minutes
    ADD CONSTRAINT card_minutes_user_id_12e511cc_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: card_project_user_id_844cfc36_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_project
    ADD CONSTRAINT card_project_user_id_844cfc36_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: card_word_project_id_96318860_fk_card_project_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_work
    ADD CONSTRAINT card_word_project_id_96318860_fk_card_project_id FOREIGN KEY (project_id) REFERENCES card_project(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: card_work_user_id_894c727b_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY card_work
    ADD CONSTRAINT card_work_user_id_894c727b_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: contest_creator_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY contest
    ADD CONSTRAINT contest_creator_fkey FOREIGN KEY (creator) REFERENCES player(playerid);


--
-- Name: django_admin_content_type_id_c4bce8eb_fk_django_content_type_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY django_admin_log
    ADD CONSTRAINT django_admin_content_type_id_c4bce8eb_fk_django_content_type_id FOREIGN KEY (content_type_id) REFERENCES django_content_type(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: django_admin_log_user_id_c564eba6_fk_auth_user_id; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY django_admin_log
    ADD CONSTRAINT django_admin_log_user_id_c564eba6_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user(id) DEFERRABLE INITIALLY DEFERRED;


--
-- Name: game_contestid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY game
    ADD CONSTRAINT game_contestid_fkey FOREIGN KEY (contestid) REFERENCES contest(contestid);


--
-- Name: game_courseid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY game
    ADD CONSTRAINT game_courseid_fkey FOREIGN KEY (courseid) REFERENCES course(courseid);


--
-- Name: game_creator_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY game
    ADD CONSTRAINT game_creator_fkey FOREIGN KEY (creator) REFERENCES player(playerid);


--
-- Name: hole_courseid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY hole
    ADD CONSTRAINT hole_courseid_fkey FOREIGN KEY (courseid) REFERENCES course(courseid);


--
-- Name: score_gameid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY score
    ADD CONSTRAINT score_gameid_fkey FOREIGN KEY (gameid) REFERENCES game(gameid);


--
-- Name: score_holeid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY score
    ADD CONSTRAINT score_holeid_fkey FOREIGN KEY (holeid) REFERENCES hole(holeid);


--
-- Name: score_playerid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: lasvirt
--

ALTER TABLE ONLY score
    ADD CONSTRAINT score_playerid_fkey FOREIGN KEY (playerid) REFERENCES player(playerid);


--
-- Name: public; Type: ACL; Schema: -; Owner: lasvirt
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM lasvirt;
GRANT ALL ON SCHEMA public TO lasvirt;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: plpgsql_call_handler(); Type: ACL; Schema: public; Owner: lasvirt
--

REVOKE ALL ON FUNCTION plpgsql_call_handler() FROM PUBLIC;
REVOKE ALL ON FUNCTION plpgsql_call_handler() FROM lasvirt;
GRANT ALL ON FUNCTION plpgsql_call_handler() TO lasvirt;
GRANT ALL ON FUNCTION plpgsql_call_handler() TO PUBLIC;


--
-- PostgreSQL database dump complete
--

