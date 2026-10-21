-- 1. Insert 100 data ke tabel users
INSERT INTO users (full_name, username, password, role, active, created_at)
SELECT
    'User ' || i,
    'user_' || i,
    '$2a$12$eImiTXuWVxfM37uY4JANjO1k.eM42.gY.y1234567890abcdefgh',
    (ARRAY['Admin', 'Member']::user_role[])[FLOOR(RANDOM() * 2 + 1)],
    TRUE,
    NOW() - (i || ' days')::INTERVAL
FROM generate_series(1, 100) AS i;

-- 2. Insert 100 data ke tabel events
INSERT INTO events (title, description, location, start_date, end_date, status, created_at)
SELECT
    'Event ' || i,
    'Deskripsi lengkap untuk event ke-' || i,
    'Gedung ' || CHR(65 + (i % 5)) || ' Lantai ' || ((i % 3) + 1),
    NOW() + (i || ' days')::INTERVAL,
    NOW() + (i || ' days')::INTERVAL + INTERVAL '3 hours',
    (ARRAY['Not started', 'in progress', 'done']::event_status[])[FLOOR(RANDOM() * 3 + 1)],
    NOW() - (i || ' days')::INTERVAL
FROM generate_series(1, 100) AS i;

-- 3. Insert 100 data ke tabel meetings
INSERT INTO meetings (event_id, title, location, start_date, end_date, status, created_at)
SELECT
    ((i - 1) % 100) + 1,
    'Meeting ' || i,
    'Ruang Rapat ' || ((i % 10) + 1),
    NOW() + (i || ' days')::INTERVAL,
    NOW() + (i || ' days')::INTERVAL + INTERVAL '1 hour',
    (ARRAY['Not started', 'in progress', 'done']::event_status[])[FLOOR(RANDOM() * 3 + 1)],
    NOW() - (i || ' days')::INTERVAL
FROM generate_series(1, 100) AS i;

-- 4. Insert 100 data ke tabel user_events
INSERT INTO user_events (user_id, event_id, role, created_at)
SELECT
    i,
    ((i - 1) % 100) + 1,
    (ARRAY['Admin', 'Member']::user_role[])[FLOOR(RANDOM() * 2 + 1)],
    NOW() - (i || ' days')::INTERVAL
FROM generate_series(1, 100) AS i;

-- 5. Insert 100 data ke tabel user_meetings
INSERT INTO user_meetings (user_id, meeting_id, attendance_method, created_at)
SELECT
    i,
    i,
    (ARRAY['Qr', 'Manually']::attendance_method[])[FLOOR(RANDOM() * 2 + 1)],
    NOW() - (i || ' days')::INTERVAL
FROM generate_series(1, 100) AS i;