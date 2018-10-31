/* Replace this file with actual dump of your database */
INSERT INTO dtb_blocposition 
    SELECT 10, 1, 2, dtb_bloc.bloc_id, 1, 1 FROM dtb_bloc WHERE filename = 'breadcrumblist';
