CREATE TABLE IF NOT EXISTS copies (
    id SERIAL PRIMARY KEY,
    date_depot TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_limite TIMESTAMP NOT NULL,
    note_brute NUMERIC(4, 2) NOT NULL CHECK (note_brute >= 0.0 AND note_brute <= 20.0),
    penalite_appliquee NUMERIC(4, 2) NOT NULL DEFAULT 0.0 CHECK (penalite_appliquee >= 0.0),
    note_finale NUMERIC(4, 2) NOT NULL CHECK (note_finale >= 0.0 AND note_finale <= 20.0),
    etudiant_nom VARCHAR(100) NULL,
    matricule VARCHAR(50) NULL,
    matiere VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_copies_date_depot ON copies(date_depot);
CREATE INDEX IF NOT EXISTS idx_copies_date_limite ON copies(date_limite);

INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale, etudiant_nom, matricule, matiere)
VALUES
    ('2026-06-15 10:00:00', '2026-06-15 12:00:00', 16.50, 0.00, 16.50, 'Alice Dupont', 'ETU001', 'Programmation Orientée Objet'),
    ('2026-06-15 14:00:00', '2026-06-15 12:00:00', 14.00, 2.00, 12.00, 'Bob Martin', 'ETU002', 'Programmation Orientée Objet');
