-- HISTORICAL RECORD ONLY: authorized cleanup already completed in EcoSphere.
-- Supabase project kslzmrddrhfyyrxyfmbw; migration 20261014001928.
-- Do not rerun or treat the prior permission as ongoing authorization.
-- Explicit RESTRICT drops abort if other objects depend on CRM tables.
BEGIN;
SET LOCAL lock_timeout = '5s';
SET LOCAL statement_timeout = '30s';
DO $scope$
DECLARE actual_tables text[];
BEGIN
  IF current_database() <> 'postgres' OR current_user <> 'postgres' THEN
    RAISE EXCEPTION 'Unexpected database or operator';
  END IF;
  SELECT array_agg(c.relname::text ORDER BY c.relname) INTO actual_tables
    FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace
    WHERE n.nspname='teralinde_crm' AND c.relkind IN ('r','p');
  IF actual_tables IS DISTINCT FROM ARRAY['cache','cache_locks','clientes','failed_jobs','job_batches','jobs','migrations','password_reset_tokens','sessions','users','visitas']::text[] THEN
    RAISE EXCEPTION 'CRM inventory changed; cleanup aborted';
  END IF;
  IF NOT EXISTS (SELECT 1 FROM pg_roles WHERE rolname='teralinde_crm_app' AND NOT rolcanlogin) THEN
    RAISE EXCEPTION 'CRM role changed; cleanup aborted';
  END IF;
END $scope$;
LOCK TABLE teralinde_crm.cache, teralinde_crm.cache_locks, teralinde_crm.clientes, teralinde_crm.failed_jobs, teralinde_crm.job_batches, teralinde_crm.jobs, teralinde_crm.migrations, teralinde_crm.password_reset_tokens, teralinde_crm.sessions, teralinde_crm.users, teralinde_crm.visitas IN ACCESS EXCLUSIVE MODE;
DO $empty$
DECLARE table_name text; row_total bigint;
BEGIN
  FOREACH table_name IN ARRAY ARRAY['cache','cache_locks','clientes','failed_jobs','job_batches','jobs','password_reset_tokens','sessions','users','visitas'] LOOP
    EXECUTE format('SELECT count(*) FROM teralinde_crm.%I',table_name) INTO row_total;
    IF row_total <> 0 THEN RAISE EXCEPTION 'Data found in %; cleanup aborted', table_name; END IF;
  END LOOP;
  IF (SELECT count(*) FROM teralinde_crm.migrations) <> 6 OR EXISTS (
    SELECT 1 FROM teralinde_crm.migrations WHERE batch <> 1 OR migration NOT IN (
      '0001_01_01_000000_create_users_table',
      '0001_01_01_000001_create_cache_table',
      '0001_01_01_000002_create_jobs_table',
      '2026_04_28_033439_create_clientes_table',
      '2026_04_28_034256_create_visitas_table',
      '2026_04_30_013959_add_observaciones_to_visitas_table'
    )
  ) THEN RAISE EXCEPTION 'Migration metadata changed; cleanup aborted'; END IF;
END $empty$;
DROP TABLE teralinde_crm.cache, teralinde_crm.cache_locks, teralinde_crm.clientes, teralinde_crm.failed_jobs, teralinde_crm.job_batches, teralinde_crm.jobs, teralinde_crm.migrations, teralinde_crm.password_reset_tokens, teralinde_crm.sessions, teralinde_crm.users, teralinde_crm.visitas RESTRICT;
DROP SCHEMA teralinde_crm RESTRICT;
DROP ROLE teralinde_crm_app;
DO $verify$
DECLARE actual record;
BEGIN
  SELECT * INTO actual FROM (SELECT
 (SELECT md5(COALESCE(jsonb_agg(jsonb_build_array(c.oid,n.nspname,c.relname,c.relkind,c.relowner,c.relrowsecurity,c.relforcerowsecurity,c.relacl::text) ORDER BY c.oid)::text,'')) FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname<>'teralinde_crm' AND n.nspname NOT IN ('pg_catalog','information_schema') AND n.nspname NOT LIKE 'pg_toast%') AS other_relations_fingerprint,
 (SELECT md5(COALESCE(jsonb_agg(jsonb_build_array(p.oid,n.nspname,p.polrelid,p.polname,p.polcmd,p.polpermissive,p.polroles::text,p.polqual::text,p.polwithcheck::text) ORDER BY p.oid)::text,'')) FROM pg_policy p JOIN pg_class c ON c.oid=p.polrelid JOIN pg_namespace n ON n.oid=c.relnamespace WHERE n.nspname<>'teralinde_crm') AS other_policies_fingerprint,
 (SELECT md5(COALESCE(jsonb_agg(jsonb_build_array(d.oid,d.defaclrole,d.defaclnamespace,d.defaclobjtype,d.defaclacl::text) ORDER BY d.oid)::text,'')) FROM pg_default_acl d LEFT JOIN pg_namespace n ON n.oid=d.defaclnamespace WHERE COALESCE(n.nspname,'')<>'teralinde_crm') AS other_default_privileges_fingerprint) fingerprints;
  IF actual.other_relations_fingerprint <> 'c9f4de06ed537a297828d71921c169ed'
     OR actual.other_policies_fingerprint <> 'fe48a4d45a73ed6d502f5831ec532832'
     OR actual.other_default_privileges_fingerprint <> 'e033f3610902ac663b2c7666c0325d3e' THEN
    RAISE EXCEPTION 'Unrelated database objects changed; cleanup aborted';
  END IF;
  IF EXISTS (SELECT 1 FROM pg_namespace WHERE nspname='teralinde_crm')
     OR EXISTS (SELECT 1 FROM pg_roles WHERE rolname='teralinde_crm_app') THEN
    RAISE EXCEPTION 'CRM objects still present; cleanup aborted';
  END IF;
END $verify$;
COMMIT;
