-- Add employees
INSERT INTO users (id, email, password, first_name, last_name, role, job_title, active, created_at, updated_at)
VALUES
  (gen_random_uuid(), 'sestra@izvjestaji.com', '$2y$12$TpU2TCBCiXUxXG.RLNfr4u2SojPaQbQCYQDkOnU5wEA.XV0DPgZAW', 'Marija', 'Marković', 'employee', 'Medicinska sestra', true, NOW(), NOW()),
  (gen_random_uuid(), 'recepcija@izvjestaji.com', '$2y$12$TpU2TCBCiXUxXG.RLNfr4u2SojPaQbQCYQDkOnU5wEA.XV0DPgZAW', 'Ana', 'Anić', 'employee', 'Recepcionar', true, NOW(), NOW())
ON CONFLICT (email) DO NOTHING;

-- Assign employees to locations
INSERT INTO staff_locations (user_id, location_id, created_at, updated_at)
SELECT u.id, l.id, NOW(), NOW()
FROM users u
CROSS JOIN locations l
WHERE u.email IN ('sestra@izvjestaji.com', 'recepcija@izvjestaji.com')
  AND u.role = 'employee'
ON CONFLICT DO NOTHING;
