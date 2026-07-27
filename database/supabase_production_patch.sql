-- MedTriaje: parche para una base Supabase creada previamente con el SQL inicial.
-- Ejecutar una sola vez desde Supabase > SQL Editor.

alter table public.triage_vital_signs
    add column if not exists respiratory_rate smallint null;

create table if not exists public.sessions (
    id varchar(255) primary key,
    user_id bigint null,
    ip_address varchar(45) null,
    user_agent text null,
    payload text not null,
    last_activity integer not null
);

create index if not exists sessions_user_id_index
    on public.sessions (user_id);

create index if not exists sessions_last_activity_index
    on public.sessions (last_activity);
