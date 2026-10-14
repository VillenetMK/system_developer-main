# Intento compartido de Supabase: retirado

Teralinde ya tiene una base propia en Neon. Consultar [Base independiente de Teralinde CRM](TERALINDE_DATABASE.md).

Con autorizacion expresa y limitada del propietario, el 13 de octubre de 2026 (Peru) se eliminaron de EcoSphere el esquema `teralinde_crm`, sus 11 tablas y el rol `teralinde_crm_app`. La verificacion posterior confirmo su ausencia y las mismas huellas para las relaciones, politicas y privilegios predeterminados ajenos al CRM. No se modificaron registros de las tablas originales de EcoSphere.

El historial Supabase conserva la creacion `20261013234503_teralinde_crm_laravel_baseline` y la limpieza `20261014001928_remove_misplaced_teralinde_crm`. La SQL de retirada se conserva en `database/history/20261014001928_remove_misplaced_teralinde_crm.sql` solo para trazabilidad.

No escribir, reutilizar ni eliminar nada en EcoSphere sin nueva autorizacion especifica. La autorizacion de esta limpieza ya se completo.

`database/bootstrap/teralinde_supabase.sql` conserva la SQL historica del intento descartado y no debe ejecutarse sobre bases existentes. La prueba actual en `database/tests/teralinde_crm.sql` corresponde exclusivamente a la base nueva de Neon.
