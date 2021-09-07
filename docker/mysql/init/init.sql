CREATE TABLE IF NOT EXISTS cards (
    id char(36) NOT NULL PRIMARY KEY,
    title varchar(255) NOT NULL UNIQUE,
    power smallint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO cards (id, title, power) VALUES
('4fdec374-833d-485c-a142-eeeb30d733b1', 'Geralt', 10),
('4fdec374-833d-485c-a142-eeeb30d733b2', 'Ciri', 9),
('4fdec374-833d-485c-a142-eeeb30d733b3', 'Vesemir', 5),
('4fdec374-833d-485c-a142-eeeb30d733b4', 'Triss', 3),
('4fdec374-833d-485c-a142-eeeb30d733b5', 'Aard sign', 0);

CREATE TABLE IF NOT EXISTS decks (
    id char(36) NOT NULL PRIMARY KEY,
    user_id char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS decks_cards (
    deck_id char(36) NOT NULL,
    card_id char(36) NOT NULL,
    title varchar(255) NOT NULL,
    power smallint UNSIGNED NOT NULL,
    amount smallint UNSIGNED NOT NULL DEFAULT 1,
    UNIQUE KEY(deck_id, card_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;