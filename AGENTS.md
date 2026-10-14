# Teralinde CRM: limite de acceso a bases de datos

Regla expresa de Gabriel: crear y gestionar una base propia para Teralinde; no modificar bases de otros proyectos.

Destino autorizado:
- Proveedor: Neon.
- Proyecto: teralinde-crm, ID `frosty-sky-96314663`.
- Branch: main, ID `br-twilight-snow-ar3etcyu`.
- Base y esquema: teralinde_crm.

Usar estos identificadores explicitos en consultas y migraciones. No crear esquemas, tablas, roles o permisos en EcoSphere, Sistema Municipal ni otras bases existentes como alternativa al aprovisionamiento de Teralinde.

La limpieza del esquema agregado anteriormente en EcoSphere requiere autorizacion expresa y acotada del propietario. No inferir esa autorizacion de una instruccion para trabajar en Teralinde.

Guardar cambios de estructura como codigo, comprobar las operaciones y permisos pertinentes y mantener secretos fuera de Git.
