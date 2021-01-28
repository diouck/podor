
   drop table formation.posts cascade;
      Create table formation.posts
   (id serial  ,
    name character varying(255) COLLATE pg_catalog."default",
    description character varying(255) COLLATE pg_catalog."default",
    email character varying(255) COLLATE pg_catalog."default",
    website character varying(255) COLLATE pg_catalog."default",
    city character varying(255) COLLATE pg_catalog."default",
    lat numeric,
    lng numeric,
	coords  text,
	 created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT post_pkey PRIMARY KEY (id)
	)
	
	 insert into formation.posts (id,name,description,email,website,city,lat,lng,coords)
   select * from dblink('dbname=bdu host =5.189.164.107 user=postgres password=Bacspace9','select id,name,
						description,email,website,city,lat::text,lng::text, concat(''['',lng,'','',lat,'']'') as coords from formation.users')as t 
    
   (id integer  ,
    name character varying(255) COLLATE pg_catalog."default",
    description character varying(255) COLLATE pg_catalog."default",
    email character varying(255) COLLATE pg_catalog."default",
    website character varying(255) COLLATE pg_catalog."default",
    city character varying(255) COLLATE pg_catalog."default",
    lat numeric,
    lng numeric ,
	coords text
	)


drop  VIEW formation.geompt_app;
CREATE OR REPLACE VIEW formation.geompt_app AS 
 SELECT id,
    name,
    description,
    city,
     website,
    email,
    
    st_setsrid(st_geomfromgeojson((' {"type":"Point",  "coordinates":'::text || coords) || '}'::text), 4326) AS geom
   FROM formation.posts;

ALTER TABLE larageom.geomlngs_app
  OWNER TO postgres;


select * from formation.posts