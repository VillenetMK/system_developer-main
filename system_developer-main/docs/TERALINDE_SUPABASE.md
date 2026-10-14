# Intento compartido de Supabase: destino descartado

Teralinde ya tiene una base propia en Neon. Consultar [Base independiente de Teralinde CRM](TERALINDE_DATABASE.md).

El esquema y el rol añadidos previamente en EcoSphere siguen alli. No escribir, reutilizar ni eliminar nada en esa base sin autorizacion especifica del propietario.

`database/bootstrap/teralinde_supabase.sql` conserva la SQL historica de aquel intento y no debe ejecutarse sobre bases existentes. La prueba actual en `database/tests/teralinde_crm.sql` corresponde exclusivamente a la base nueva de Neon.
