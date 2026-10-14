# Teralinde CRM: limite de acceso a bases de datos

Regla expresa de Gabriel: crear y gestionar una base propia para Teralinde; no modificar bases de otros proyectos.

Destino autorizado:
- Proveedor: Neon.
- Proyecto: teralinde-crm, ID `frosty-sky-96314663`.
- Branch: main, ID `br-twilight-snow-ar3etcyu`.
- Base y esquema: teralinde_crm.

Usar estos identificadores explicitos en consultas y migraciones. No crear esquemas, tablas, roles o permisos en EcoSphere, Sistema Municipal ni otras bases existentes como alternativa al aprovisionamiento de Teralinde.

El propietario autorizo expresamente retirar de EcoSphere solo el esquema `teralinde_crm` y el rol `teralinde_crm_app`. La limpieza se completo y verifico el 13 de octubre de 2026 (Peru). Esa autorizacion puntual ya esta consumida: no habilita nuevas escrituras en EcoSphere ni en otras bases.

Guardar cambios de estructura como codigo, comprobar las operaciones y permisos pertinentes y mantener secretos fuera de Git.
