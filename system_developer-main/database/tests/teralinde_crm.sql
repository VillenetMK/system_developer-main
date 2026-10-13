BEGIN;
GRANT teralinde_crm_app TO postgres WITH SET TRUE;
SET LOCAL ROLE teralinde_crm_app;
DO $test$
DECLARE
 uid bigint;
 cid bigint;
 vid bigint;
 fk_rejected boolean := false;
 state_rejected boolean := false;
 email_rejected boolean := false;
BEGIN
 INSERT INTO teralinde_crm.users(name,email,password) VALUES ('Prueba temporal','crm-check@example.invalid','not-a-login-password') RETURNING id INTO uid;
 INSERT INTO teralinde_crm.clientes(dni_ruc,nombre_completo,nro_celular,correo,direccion) VALUES ('00000000','Cliente ficticio de verificacion','000000000','crm-check@example.invalid','Direccion ficticia') RETURNING id INTO cid;
 INSERT INTO teralinde_crm.visitas(cliente_id,dia,hora_estimada,direccion,detalle) VALUES (cid,'2026-10-13','12:00:00','Direccion ficticia','Prueba temporal') RETURNING id INTO vid;
 IF (SELECT estado FROM teralinde_crm.visitas WHERE id=vid) <> 'en proceso' THEN RAISE EXCEPTION 'Estado inicial incorrecto'; END IF;
 UPDATE teralinde_crm.visitas SET estado='culminada',observaciones='Prueba temporal completada' WHERE id=vid;
 IF NOT EXISTS(SELECT 1 FROM teralinde_crm.visitas WHERE id=vid AND estado='culminada' AND observaciones='Prueba temporal completada') THEN RAISE EXCEPTION 'Actualizacion no persistida'; END IF;
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
 IF has_schema_privilege(current_user,'teralinde_crm','CREATE') THEN RAISE EXCEPTION 'El rol de ejecucion puede modificar la estructura'; END IF;
 IF has_table_privilege(current_user,'teralinde_crm.migrations','INSERT') THEN RAISE EXCEPTION 'El rol de ejecucion puede alterar el historial de migraciones'; END IF;
 PERFORM set_config('teralinde_crm.test_results','{"runtime_crud":true,"default_state":true,"foreign_key":true,"allowed_states":true,"unique_email":true,"cascade":true,"migration_history_read":true,"runtime_cannot_create_tables":true,"runtime_cannot_write_migrations":true}',true);
END $test$;
RESET ROLE;
SELECT current_setting('teralinde_crm.test_results')::jsonb AS passed_tests;
ROLLBACK;