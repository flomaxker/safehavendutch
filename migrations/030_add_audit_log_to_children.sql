-- Add audit_log column to children table for basic change history
ALTER TABLE children
  ADD COLUMN audit_log TEXT NULL DEFAULT NULL AFTER notes;

