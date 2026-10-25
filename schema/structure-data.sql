    DO
    $$
        BEGIN
            -- Cek dan buat tipe 'user_role'
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_role') THEN
                CREATE TYPE user_role AS ENUM ('Admin', 'Member');
            END IF;

            -- Cek dan buat tipe 'event_status' (perbaikan typo: 'in progress')
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'event_status') THEN
                CREATE TYPE event_status AS ENUM ('Not started', 'in progress', 'done');
            END IF;

            -- Cek dan buat tipe 'attendance_method'
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'attendance_method') THEN
                CREATE TYPE attendance_method AS ENUM ('Qr', 'Manually');
            END IF;
        END
    $$;

-- 1. Entitas USERS
CREATE TABLE IF NOT EXISTS users
(
    id         SERIAL PRIMARY KEY,
    full_name  VARCHAR(255) NOT NULL,
    username   VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       user_role DEFAULT 'Member',
    active     BOOLEAN   DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT NULL
);

-- Unique index parsial agar username yang sudah di-soft delete tidak menghalangi registrasi baru dengan username yang sama
CREATE UNIQUE INDEX IF NOT EXISTS idx_users_unique_username_active
    ON users (username)
    WHERE deleted_at IS NULL;

-- 2. Entitas EVENT
CREATE TABLE IF NOT EXISTS events
(
    id          SERIAL PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    location    VARCHAR(255),
    start_date  TIMESTAMP,
    end_date    TIMESTAMP,
    status      event_status DEFAULT 'Not started',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    deleted_at  TIMESTAMP    DEFAULT NULL
);

-- 3. Entitas MEETING & Relasi MEMILIKI (1:N)
CREATE TABLE IF NOT EXISTS meetings
(
    id         SERIAL PRIMARY KEY,
    event_id   INT          NOT NULL REFERENCES events (id) ON DELETE CASCADE,
    title      VARCHAR(255) NOT NULL,
    location   VARCHAR(255),
    start_date TIMESTAMP,
    end_date   TIMESTAMP,
    status     event_status DEFAULT 'Not started',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP    DEFAULT NULL
);

-- 4. Relasi MENJADI (M:N)
CREATE TABLE IF NOT EXISTS user_events
(
    id         SERIAL PRIMARY KEY,
    user_id    INT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    event_id   INT NOT NULL REFERENCES events (id) ON DELETE CASCADE,
    role       user_role,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT NULL
);

-- Constraint unik parsial untuk mencegah pendaftaran ganda pada event yang sama jika data masih aktif
CREATE UNIQUE INDEX IF NOT EXISTS idx_user_events_active
    ON user_events (user_id, event_id)
    WHERE deleted_at IS NULL;

-- 5. Relasi MENGIKUTI (M:N) / Presensi
CREATE TABLE IF NOT EXISTS user_meetings
(
    id                SERIAL PRIMARY KEY,
    user_id           INT               NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    meeting_id        INT               NOT NULL REFERENCES meetings (id) ON DELETE CASCADE,
    attendance_method attendance_method NOT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at        TIMESTAMP DEFAULT NULL
);

-- Constraint unik parsial untuk presensi aktif
CREATE UNIQUE INDEX IF NOT EXISTS idx_user_meetings_active
    ON user_meetings (user_id, meeting_id)
    WHERE deleted_at IS NULL;