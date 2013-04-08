SET @@session.sql_mode = '';

#making EN as default lang
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'sDefaultLang';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba832f744c5786a371d9df33778f956ef30fb1e8bb85d97b3b5de43e6bad688dfc6f63a8af34b33290cdd6fc889c8e77cfee0e8a17ade6b94130fda30d062d03e35d8d1bda1c2dc4dd5281fcb1c9538cf114050a3e7118e16151bfe94f5a0706d2eb3d9ff8b4a24f88963788f5dd1c33c573a1ebe3f5b06c072c6a373aaecb11755d907b50a79bbac613054871af686a7d3dbe0b6e1a3e292a109e2f5bc31bcd26ebbe42dac8c9cac3fa53c6fae3c8c7c3c113a4f1a8823d13c78c27dc WHERE `OXVARNAME` = 'aLanguageParams';
