/*
 Navicat Premium Dump SQL

 Source Server         : TEEJOSH
 Source Server Type    : PostgreSQL
 Source Server Version : 170006 (170006)
 Source Host           : aws-1-sa-east-1.pooler.supabase.com:5432
 Source Catalog        : db_teejosh
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 170006 (170006)
 File Encoding         : 65001

 Date: 01/12/2025 16:14:16
*/


-- ----------------------------
-- Sequence structure for categoria_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."categoria_id_seq";
CREATE SEQUENCE "public"."categoria_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for cliente_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."cliente_id_seq";
CREATE SEQUENCE "public"."cliente_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for edicion_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."edicion_id_seq";
CREATE SEQUENCE "public"."edicion_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for franquicia_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."franquicia_id_seq";
CREATE SEQUENCE "public"."franquicia_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for item_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."item_id_seq";
CREATE SEQUENCE "public"."item_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for lenguaje_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."lenguaje_id_seq";
CREATE SEQUENCE "public"."lenguaje_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for producto_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."producto_id_seq";
CREATE SEQUENCE "public"."producto_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for reg_venta_id_reg_venta_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."reg_venta_id_reg_venta_seq";
CREATE SEQUENCE "public"."reg_venta_id_reg_venta_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1;

-- ----------------------------
-- Table structure for categoria
-- ----------------------------
DROP TABLE IF EXISTS "public"."categoria";
CREATE TABLE "public"."categoria" (
  "id" int4 NOT NULL DEFAULT nextval('categoria_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Records of categoria
-- ----------------------------
INSERT INTO "public"."categoria" VALUES (1, 'Cartas TCG');
INSERT INTO "public"."categoria" VALUES (2, 'Juguetes');
INSERT INTO "public"."categoria" VALUES (3, 'Figuras');
INSERT INTO "public"."categoria" VALUES (4, 'Autos');
INSERT INTO "public"."categoria" VALUES (5, 'Juegos de Mesa');

-- ----------------------------
-- Table structure for cliente
-- ----------------------------
DROP TABLE IF EXISTS "public"."cliente";
CREATE TABLE "public"."cliente" (
  "id" int4 NOT NULL DEFAULT nextval('cliente_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL,
  "apellido" varchar(60) COLLATE "pg_catalog"."default" NOT NULL,
  "dni" varchar(8) COLLATE "pg_catalog"."default" NOT NULL,
  "telefono" varchar(20) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of cliente
-- ----------------------------

-- ----------------------------
-- Table structure for edicion
-- ----------------------------
DROP TABLE IF EXISTS "public"."edicion";
CREATE TABLE "public"."edicion" (
  "id" int4 NOT NULL DEFAULT nextval('edicion_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Records of edicion
-- ----------------------------
INSERT INTO "public"."edicion" VALUES (1, '1ra Edición');
INSERT INTO "public"."edicion" VALUES (2, '2da Edición');
INSERT INTO "public"."edicion" VALUES (3, '3ra Edición');

-- ----------------------------
-- Table structure for franquicia
-- ----------------------------
DROP TABLE IF EXISTS "public"."franquicia";
CREATE TABLE "public"."franquicia" (
  "id" int4 NOT NULL DEFAULT nextval('franquicia_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Records of franquicia
-- ----------------------------
INSERT INTO "public"."franquicia" VALUES (1, 'Pokémon');
INSERT INTO "public"."franquicia" VALUES (2, 'Magic The Gathering');
INSERT INTO "public"."franquicia" VALUES (3, 'One Piece');
INSERT INTO "public"."franquicia" VALUES (4, 'Yu-Gi-Oh!');
INSERT INTO "public"."franquicia" VALUES (5, 'Hot Wheels');
INSERT INTO "public"."franquicia" VALUES (6, 'Funko');
INSERT INTO "public"."franquicia" VALUES (7, 'Dragon Ball');
INSERT INTO "public"."franquicia" VALUES (8, 'Digimon');

-- ----------------------------
-- Table structure for item
-- ----------------------------
DROP TABLE IF EXISTS "public"."item";
CREATE TABLE "public"."item" (
  "id" int4 NOT NULL DEFAULT nextval('item_id_seq'::regclass),
  "id_producto" int4 NOT NULL,
  "precio" numeric(6,2) NOT NULL,
  "cantidad" int4 NOT NULL,
  "id_edicion" int4,
  "id_lenguaje" int4 NOT NULL,
  "fecha_ingreso" timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP
)
;

-- ----------------------------
-- Records of item
-- ----------------------------
INSERT INTO "public"."item" VALUES (4, 2, 22.00, 60, 1, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (7, 4, 15.00, 50, 1, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (8, 5, 45.00, 30, NULL, 2, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (10, 7, 10.00, 200, NULL, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (12, 9, 75.00, 10, NULL, 2, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (11, 8, 120.00, 13, NULL, 2, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (14, 11, 1033.32, 1, 1, 3, '2025-10-08 23:15:55.334914');
INSERT INTO "public"."item" VALUES (3, 1, 18.50, 74, 2, 2, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (2, 1, 18.50, 74, 1, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (9, 6, 45.00, 24, NULL, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (13, 10, 17.00, 50, 3, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (5, 3, 19.00, 115, 1, 1, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (6, 3, 19.00, 115, 2, 2, '2025-10-08 02:27:28.244526');
INSERT INTO "public"."item" VALUES (15, 12, 700.00, 4, 1, 2, '2025-10-10 05:22:59.360134');

-- ----------------------------
-- Table structure for lenguaje
-- ----------------------------
DROP TABLE IF EXISTS "public"."lenguaje";
CREATE TABLE "public"."lenguaje" (
  "id" int4 NOT NULL DEFAULT nextval('lenguaje_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL
)
;

-- ----------------------------
-- Records of lenguaje
-- ----------------------------
INSERT INTO "public"."lenguaje" VALUES (1, 'Inglés');
INSERT INTO "public"."lenguaje" VALUES (2, 'Español');
INSERT INTO "public"."lenguaje" VALUES (3, 'Francés');

-- ----------------------------
-- Table structure for producto
-- ----------------------------
DROP TABLE IF EXISTS "public"."producto";
CREATE TABLE "public"."producto" (
  "id" int4 NOT NULL DEFAULT nextval('producto_id_seq'::regclass),
  "nombre" varchar(60) COLLATE "pg_catalog"."default" NOT NULL,
  "descripcion" text COLLATE "pg_catalog"."default",
  "id_categoria" int4 NOT NULL,
  "id_franquicia" int4 NOT NULL
)
;

-- ----------------------------
-- Records of producto
-- ----------------------------
INSERT INTO "public"."producto" VALUES (1, 'Booster Pack Pokémon Escarlata y Púrpura', 'Sobre de 10 cartas coleccionables de la expansión Escarlata y Púrpura.', 1, 1);
INSERT INTO "public"."producto" VALUES (2, 'Booster Pack Magic The Gathering Dominaria United', 'Sobre de 15 cartas de la expansión Dominaria United.', 1, 2);
INSERT INTO "public"."producto" VALUES (3, 'Booster Pack One Piece OP06', 'Sobre de 12 cartas coleccionables del set OP06 de One Piece TCG.', 1, 3);
INSERT INTO "public"."producto" VALUES (4, 'Booster Pack Yu-Gi-Oh! Legend of Blue Eyes', 'Sobre clásico con cartas del primer set de Yu-Gi-Oh!', 1, 4);
INSERT INTO "public"."producto" VALUES (5, 'Figura Funko Pikachu', 'Figura coleccionable Funko Pop de Pikachu.', 3, 6);
INSERT INTO "public"."producto" VALUES (6, 'Figura Funko Luffy', 'Figura Funko Pop de Monkey D. Luffy.', 3, 6);
INSERT INTO "public"."producto" VALUES (7, 'Hot Wheels 1970 Dodge Charger', 'Auto coleccionable a escala 1:64.', 4, 5);
INSERT INTO "public"."producto" VALUES (8, 'Juego de Mesa Pokémon Monopoly', 'Versión temática de Monopoly con personajes Pokémon.', 5, 1);
INSERT INTO "public"."producto" VALUES (9, 'Figura Goku Super Saiyan', 'Figura articulada de Goku en modo Super Saiyan.', 3, 7);
INSERT INTO "public"."producto" VALUES (10, 'Booster Pack Digimon BT13', 'Sobre de 12 cartas de la expansión BT13 de Digimon Card Game.', 1, 8);
INSERT INTO "public"."producto" VALUES (11, 'Figura Funko Stormtrooper', '¡FUNKO POP! Stormtrooper #510 exclusivo de celebración de Star Wars 2022 PSA 8,5-', 3, 6);
INSERT INTO "public"."producto" VALUES (12, 'Booster Display Pokémon Escarlata y Púrpura', 'Caja de 36 sobre de refuerzo de la expansión Escarlata y Púrpura', 1, 1);
INSERT INTO "public"."producto" VALUES (13, 'Dummy', 'aa', 1, 2);

-- ----------------------------
-- Table structure for producto_venta
-- ----------------------------
DROP TABLE IF EXISTS "public"."producto_venta";
CREATE TABLE "public"."producto_venta" (
  "id_producto" int4,
  "id_reg_venta" int4,
  "cantidad" int4,
  "monto" numeric(10,2)
)
;

-- ----------------------------
-- Records of producto_venta
-- ----------------------------
INSERT INTO "public"."producto_venta" VALUES (6, 4, 5, 225.00);
INSERT INTO "public"."producto_venta" VALUES (8, 5, 2, 240.00);
INSERT INTO "public"."producto_venta" VALUES (8, 5, 2, 240.00);
INSERT INTO "public"."producto_venta" VALUES (11, 6, 3, 3099.96);
INSERT INTO "public"."producto_venta" VALUES (11, 7, 4, 4133.28);
INSERT INTO "public"."producto_venta" VALUES (11, 7, 4, 4133.28);
INSERT INTO "public"."producto_venta" VALUES (12, 9, 1, 700.00);
INSERT INTO "public"."producto_venta" VALUES (10, 9, 5, 85.00);
INSERT INTO "public"."producto_venta" VALUES (12, 9, 1, 700.00);
INSERT INTO "public"."producto_venta" VALUES (10, 9, 5, 85.00);
INSERT INTO "public"."producto_venta" VALUES (3, 10, 3, 57.00);
INSERT INTO "public"."producto_venta" VALUES (1, 10, 6, 111.00);
INSERT INTO "public"."producto_venta" VALUES (6, 10, 1, 45.00);
INSERT INTO "public"."producto_venta" VALUES (3, 10, 3, 57.00);
INSERT INTO "public"."producto_venta" VALUES (1, 10, 6, 111.00);
INSERT INTO "public"."producto_venta" VALUES (6, 10, 1, 45.00);
INSERT INTO "public"."producto_venta" VALUES (10, 11, 5, 85.00);
INSERT INTO "public"."producto_venta" VALUES (3, 11, 2, 38.00);
INSERT INTO "public"."producto_venta" VALUES (12, 12, 1, 700.00);

-- ----------------------------
-- Table structure for reg_venta
-- ----------------------------
DROP TABLE IF EXISTS "public"."reg_venta";
CREATE TABLE "public"."reg_venta" (
  "id_reg_venta" int4 NOT NULL DEFAULT nextval('reg_venta_id_reg_venta_seq'::regclass),
  "monto_total" numeric(10,2) NOT NULL,
  "fecha" date,
  "hora" time(6),
  "m_pago" text COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Records of reg_venta
-- ----------------------------
INSERT INTO "public"."reg_venta" VALUES (1, 0.00, '2025-11-21', '06:35:09.703812', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (4, 225.00, '2025-11-21', '08:20:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (5, 240.00, '2025-11-21', '08:24:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (6, 3099.96, '2025-11-21', '08:59:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (7, 4133.28, '2025-11-21', '09:04:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (8, 2066.64, '2025-11-21', '09:08:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (9, 785.00, '2025-11-21', '09:08:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (10, 213.00, '2025-11-21', '09:10:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (11, 123.00, '2025-11-21', '09:25:00', 'Efectivo');
INSERT INTO "public"."reg_venta" VALUES (12, 700.00, '2025-11-21', '09:30:00', 'Efectivo');

-- ----------------------------
-- Function structure for fn_modificar_producto
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."fn_modificar_producto"("p_id" int4, "p_nombre" text, "p_descripcion" text, "p_id_categoria" int4, "p_id_franquicia" int4);
CREATE FUNCTION "public"."fn_modificar_producto"("p_id" int4, "p_nombre" text, "p_descripcion" text, "p_id_categoria" int4, "p_id_franquicia" int4)
  RETURNS "pg_catalog"."text" AS $BODY$
DECLARE
    existe INT;
BEGIN
    SELECT COUNT(*) INTO existe FROM producto WHERE id = p_id;
    IF existe = 0 THEN
        RETURN 'Error: El producto no existe.';
    END IF;

    UPDATE producto
    SET nombre = p_nombre,
        descripcion = p_descripcion,
        id_categoria = p_id_categoria,
        id_franquicia = p_id_franquicia
    WHERE id = p_id;

    RETURN 'Producto actualizado correctamente.';
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- ----------------------------
-- Function structure for fn_reabastecer_item
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."fn_reabastecer_item"("p_item_id" int4, "p_cantidad_agregada" int4);
CREATE FUNCTION "public"."fn_reabastecer_item"("p_item_id" int4, "p_cantidad_agregada" int4)
  RETURNS "pg_catalog"."text" AS $BODY$
DECLARE
    stock_actual INT;
BEGIN
    IF p_cantidad_agregada <= 0 THEN
        RETURN 'Error: la cantidad a agregar debe ser positiva.';
    END IF;

    SELECT cantidad INTO stock_actual FROM item WHERE id = p_item_id;

    IF stock_actual IS NULL THEN
        RETURN 'Error: el item especificado no existe.';
    END IF;

    UPDATE item
    SET cantidad = cantidad + p_cantidad_agregada
    WHERE id = p_item_id;

    RETURN format('Se añadieron %s unidades. Stock actual: %s.',
        p_cantidad_agregada, stock_actual + p_cantidad_agregada);
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- ----------------------------
-- Function structure for fn_restock_item
-- ----------------------------
DROP FUNCTION IF EXISTS "public"."fn_restock_item"("p_item_fuente" int4, "p_item_destino" int4, "p_unidades_por_item" int4, "p_cantidad" int4);
CREATE FUNCTION "public"."fn_restock_item"("p_item_fuente" int4, "p_item_destino" int4, "p_unidades_por_item" int4, "p_cantidad" int4)
  RETURNS "pg_catalog"."text" AS $BODY$
DECLARE
    stock_fuente INT;
    stock_destino INT;
BEGIN
    IF NOT EXISTS (SELECT 1 FROM item WHERE id = p_item_fuente) THEN
        RETURN 'Error: El item fuente no existe.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM item WHERE id = p_item_destino) THEN
        RETURN 'Error: El item destino no existe.';
    END IF;

    SELECT cantidad INTO stock_fuente FROM item WHERE id = p_item_fuente;
    SELECT cantidad INTO stock_destino FROM item WHERE id = p_item_destino;

    IF stock_fuente < p_cantidad THEN
        RETURN 'Error: No hay suficiente cantidad del item fuente.';
    END IF;

    UPDATE item
    SET cantidad = cantidad - p_cantidad
    WHERE id = p_item_fuente;

    UPDATE item
    SET cantidad = cantidad + (p_cantidad * p_unidades_por_item)
    WHERE id = p_item_destino;

    RETURN format(
        'Se han abierto %s unidades del item fuente (ID %s), generando %s nuevas unidades del item destino (ID %s).',
        p_cantidad, p_item_fuente, p_cantidad * p_unidades_por_item, p_item_destino
    );
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."categoria_id_seq"
OWNED BY "public"."categoria"."id";
SELECT setval('"public"."categoria_id_seq"', 5, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."cliente_id_seq"
OWNED BY "public"."cliente"."id";
SELECT setval('"public"."cliente_id_seq"', 1, false);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."edicion_id_seq"
OWNED BY "public"."edicion"."id";
SELECT setval('"public"."edicion_id_seq"', 3, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."franquicia_id_seq"
OWNED BY "public"."franquicia"."id";
SELECT setval('"public"."franquicia_id_seq"', 8, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."item_id_seq"
OWNED BY "public"."item"."id";
SELECT setval('"public"."item_id_seq"', 16, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."lenguaje_id_seq"
OWNED BY "public"."lenguaje"."id";
SELECT setval('"public"."lenguaje_id_seq"', 2, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."producto_id_seq"
OWNED BY "public"."producto"."id";
SELECT setval('"public"."producto_id_seq"', 10, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."reg_venta_id_reg_venta_seq"
OWNED BY "public"."reg_venta"."id_reg_venta";
SELECT setval('"public"."reg_venta_id_reg_venta_seq"', 12, true);

-- ----------------------------
-- Primary Key structure for table categoria
-- ----------------------------
ALTER TABLE "public"."categoria" ADD CONSTRAINT "pk_categoria_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table cliente
-- ----------------------------
ALTER TABLE "public"."cliente" ADD CONSTRAINT "pk_cliente_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table edicion
-- ----------------------------
ALTER TABLE "public"."edicion" ADD CONSTRAINT "pk_edicion_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table franquicia
-- ----------------------------
ALTER TABLE "public"."franquicia" ADD CONSTRAINT "pk_franquicia_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table item
-- ----------------------------
ALTER TABLE "public"."item" ADD CONSTRAINT "pk_item_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table lenguaje
-- ----------------------------
ALTER TABLE "public"."lenguaje" ADD CONSTRAINT "pk_lenguaje_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table producto
-- ----------------------------
ALTER TABLE "public"."producto" ADD CONSTRAINT "pk_producto_id" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table reg_venta
-- ----------------------------
ALTER TABLE "public"."reg_venta" ADD CONSTRAINT "reg_venta_pkey" PRIMARY KEY ("id_reg_venta");

-- ----------------------------
-- Foreign Keys structure for table item
-- ----------------------------
ALTER TABLE "public"."item" ADD CONSTRAINT "fk_item_id_edicion_edicion_id" FOREIGN KEY ("id_edicion") REFERENCES "public"."edicion" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE "public"."item" ADD CONSTRAINT "fk_item_id_lenguaje_lenguaje_id" FOREIGN KEY ("id_lenguaje") REFERENCES "public"."lenguaje" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE "public"."item" ADD CONSTRAINT "fk_item_id_producto_producto_id" FOREIGN KEY ("id_producto") REFERENCES "public"."producto" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;

-- ----------------------------
-- Foreign Keys structure for table producto
-- ----------------------------
ALTER TABLE "public"."producto" ADD CONSTRAINT "fk_producto_id_categoria_categoria_id" FOREIGN KEY ("id_categoria") REFERENCES "public"."categoria" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE "public"."producto" ADD CONSTRAINT "fk_producto_id_franquicia_franquicia_id" FOREIGN KEY ("id_franquicia") REFERENCES "public"."franquicia" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;

-- ----------------------------
-- Foreign Keys structure for table producto_venta
-- ----------------------------
ALTER TABLE "public"."producto_venta" ADD CONSTRAINT "producto_venta_id_producto_fkey" FOREIGN KEY ("id_producto") REFERENCES "public"."producto" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE "public"."producto_venta" ADD CONSTRAINT "producto_venta_id_reg_venta_fkey" FOREIGN KEY ("id_reg_venta") REFERENCES "public"."reg_venta" ("id_reg_venta") ON DELETE NO ACTION ON UPDATE NO ACTION;
