-- Table: public.bins

-- DROP TABLE public.bins;

CREATE TABLE public.bins
(
    name character varying(1024) COLLATE pg_catalog."default",
    code character(3) COLLATE pg_catalog."default",
    bin character(6) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT bins_pkey PRIMARY KEY (bin)
)

TABLESPACE pg_default;

ALTER TABLE public.bins
    OWNER to postgres;