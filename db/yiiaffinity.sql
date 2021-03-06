DROP TABLE IF EXISTS generos CASCADE;

CREATE TABLE generos
(
    id     BIGSERIAL    PRIMARY KEY
  , genero VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS peliculas CASCADE;

CREATE TABLE peliculas
(
    id         BIGSERIAL    PRIMARY KEY
  , titulo     VARCHAR(255) NOT NULL
  , anyo       NUMERIC(4)
  , sinopsis   TEXT
  , precio     NUMERIC(5,2) CONSTRAINT ck_peliculas_precio_positivo
                            CHECK (coalesce(precio, 0)>= 0)
  , duracion   SMALLINT     DEFAULT 0
                            CONSTRAINT ck_peliculas_duracion_positiva
                            CHECK (coalesce(duracion, 0) >= 0)
  , genero_id  BIGINT       NOT NULL
                            REFERENCES generos (id)
                            ON DELETE NO ACTION
                            ON UPDATE CASCADE
  , created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS personas CASCADE;

CREATE TABLE personas
(
      id     BIGSERIAL    PRIMARY KEY
    , nombre VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS papeles CASCADE;

CREATE TABLE papeles
(
      id    BIGSERIAL    PRIMARY KEY
    , papel VARCHAR(255) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id       BIGSERIAL   PRIMARY KEY
  , login    VARCHAR(50) NOT NULL UNIQUE
                         CONSTRAINT ck_login_sin_espacios
                         CHECK (login NOT LIKE '% %')
  , password VARCHAR(60) NOT NULL
);

DROP TABLE IF EXISTS participaciones CASCADE;

CREATE TABLE participaciones
(
      pelicula_id BIGSERIAL REFERENCES peliculas(id)
    , persona_id  BIGSERIAL REFERENCES personas(id)
    , papel_id    BIGSERIAL REFERENCES papeles(id)
    , PRIMARY KEY(pelicula_id, persona_id, papel_id)
);

-- INSERT

INSERT INTO usuarios (login, password)
VALUES ('pepe', crypt('pepe', gen_salt('bf', 10)))
     , ('admin', crypt('admin', gen_salt('bf', 10)));

INSERT INTO generos (genero)
VALUES ('Comedia')
     , ('Terror')
     , ('Ciencia-Ficción')
     , ('Drama')
     , ('Aventuras');

INSERT INTO peliculas (titulo, anyo, sinopsis, precio, duracion, genero_id)
VALUES ('Los últimos Jedi', 2017, 'Va uno y se cae...', 15, 204,  3)
     , ('Los Goonies', 1985, 'Unos niños encuentran un tesoro', 20, 120, 5)
     , ('Aquí llega Condemor', 1996, 'Mejor no cuento nada...', 12.50, 90,  1);

INSERT INTO personas (nombre)
VALUES ('Eustaquio')
     , ('Rodolfo')
     , ('Juan')
     , ('Pepe')
     , ('María');

INSERT INTO papeles (papel)
VALUES ('Director')
     , ('Actor')
     , ('Director de fotografia')
     , ('Productor');

INSERT INTO participaciones (pelicula_id, persona_id, papel_id)
     VALUES (1,1,1)
          , (1,2,3)
          , (2,2,2)
          , (2,2,3)
          , (3,3,3);
