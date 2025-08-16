-- Seed default settings (idempotent)
INSERT INTO settings (setting_key, setting_value) VALUES ('tinymce_api_key', '')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO settings (setting_key, setting_value) VALUES ('site_logo', '/assets/images/default-logo.png')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO settings (setting_key, setting_value) VALUES ('hero_image', '/assets/images/default-hero.jpg')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

