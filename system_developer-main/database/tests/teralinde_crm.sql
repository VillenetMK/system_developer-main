-- Prueba inicial: solo en el proyecto Neon frosty-sky-96314663, base nueva teralinde_crm.
-- Los registros ficticios se revierten dentro de una subtransaccion.
BEGIN;
SET LOCAL ROLE teralinde_crm_app;
DO $test$
DECLARE
 uid bigint;
 cid bigint;
 vid bigint;
 fk_rejected boolean := false;
 state_rejected boolean := false;
 email_rejected boolean := false;
 passed jsonb;
BEGIN
 IF current_database() <> 'teralinde_crm' THEN RAISE EXCEPTION 'La prueba solo admite la base independiente teralinde_crm'; END IF;
 IF current_user <> 'teralinde_crm_app' THEN RAISE EXCEPTION 'La prueba debe usar el rol de backend'; END IF;
 IF EXISTS(SELECT 1 FROM teralinde_crm.clientes) OR EXISTS(SELECT 1 FROM teralinde_crm.visitas) OR EXISTS(SELECT 1 FROM teralinde_crm.users) THEN RAISE EXCEPTION 'La prueba inicial exige tablas nuevas y vacias'; END IF;
 BEGIN
  INSERT INTO teralinde_crm.users(name,email,password) VALUES ('Prueba temporal','crm-check@example.invalid','not-a-login-password') RETURNING id INTO uid;
  INSERT INTO teralinde_crm.clientes(dni_ruc,nombre_completo,nro_celular,correo,direccion) VALUES ('00000000','Cliente ficticio de verificacion','000000000','crm-check@example.invalid','Direccion ficticia') RETURNING id INTO cid;
  INSERT INTO teralinde_crm.visitas(cliente_id,dia,hora_estimada,direccion,detalle) VALUES (cid,'2026-10-13','12:00:00','Direccion ficticia','Prueba temporal') RETURNING id INTO vid;
  IF (SELECT estado FROM teralinde_crm.visitas WHERE id=vid) <> 'en proceso' THEN RAISE EXCEPTION 'Estado inicial incorrecto'; END IF;
  UPDATE teralinde_crm.visitas SET estado='culminada',observaciones='Prueba temporal completada' WHERE id=vid;
  IF NOT EXISTS(SELECT 1 FROM teralinde_crm.visitas WHERE id=vid AND estado='culminada' AND observaciones='Prueba temporal completada') THEN RAISE EXCEPTION 'Actualizacion incorrecta'; END IF;
  BEGIN
   INSERT INTO teralinde_crm.visitas(cliente_id,dia,hora_estimada,direccion,detalle) VALUES (-1,'2026-10-13','12:00:00','Ficticia','Referencia inexistente');
  EXCEPTION WHEN foreign_key_violation THEN fk_rejected:=true;
  END;
  BEGIN
   UPDATE teralinde_crm.visitas SET estado='estado_invalido' WHERE id=vid;
  EXCEPTION WHEN check_violation THEN state_rejected:=true;
  END;
  BEGIN
   INSERT INTO teralinde_crm.users(name,email,password) VALUES ('Duplicado ficticio','crm-check@example.invalid','not-a-login-password');
  EXCEPTION WHEN unique_violation THEN email_rejected:=true;
  END;
  IF NOT (fk_rejected AND state_rejected AND email_rejected) THEN RAISE EXCEPTION 'Fallo de integridad'; END IF;
  DELETE FROM teralinde_crm.clientes WHERE id=cid;
  IF EXISTS(SELECT 1 FROM teralinde_crm.visitas WHERE id=vid) THEN RAISE EXCEPTION 'La relacion no elimina las visitas del cliente'; END IF;
  DELETE FROM teralinde_crm.users WHERE id=uid;
  IF (SELECT count(*) FROM teralinde_crm.migrations) <> 6 THEN RAISE EXCEPTION 'Historial Laravel incorrecto'; END IF;
  IF has_schema_privilege(current_user,'teralinde_crm','CREATE') THEN RAISE EXCEPTION 'El backend puede modificar la estructura'; END IF;
  IF has_table_privilege(current_user,'teralinde_crm.migrations','INSERT') THEN RAISE EXCEPTION 'El backend puede alterar el historial'; END IF;
  passed:='{"runtime_crud":true,"default_state":true,"foreign_key":true,"allowed_states":true,"unique_email":true,"cascade":true,"migration_history_read":true,"runtime_cannot_create_tables":true,"runtime_cannot_write_migrations":true}'::jsonb;
  RAISE SQLSTATE 'PT001' USING MESSAGE='Revertir exclusivamente la subtransaccion de prueba';
 EXCEPTION WHEN SQLSTATE 'PT001' THEN NULL;
 END;
 IF passed IS NULL THEN RAISE EXCEPTION 'Prueba sin resultado'; END IF;
 IF EXISTS(SELECT 1 FROM teralinde_crm.clientes) OR EXISTS(SELECT 1 FROM teralinde_crm.visitas) OR EXISTS(SELECT 1 FROM teralinde_crm.users) THEN RAISE EXCEPTION 'Quedaron datos de prueba'; END IF;
 PERFORM set_config('teralinde_crm.test_results',passed::text,true);
END $test$;
SELECT current_setting('teralinde_crm.test_results')::jsonb AS passed_tests;
RESET ROLE;
ROLLBACK;
