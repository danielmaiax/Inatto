begin;

--
-- select * from click limit 1000;
-- update click set url = replace(url, '', '');

-- SELECT url, SUBSTRING_INDEX(url, '?', 1) from click where 1 and url like '%?%' and url like '%convenio%';
-- SELECT url, SUBSTRING_INDEX(url, '?eNort', 1) from click where 1 and url like '%?eNort%' and url like '%convenio%' limit 1000;
-- SELECT url, SUBSTRING_INDEX(url, 'enort', 1) from click where 1 and url like '%enort%' and url like '%convenio%' limit 1000;
-- SELECT url from click where 1 and url like '%convenio%' limit 1000;
-- SELECT url from click where 1 and url like '%convenio%' limit 1000;

update click
set url = SUBSTRING_INDEX(url, '?enort', 1)
where 1
  and url like '%convenio%';
update click
set url = SUBSTRING_INDEX(url, 'enort', 1)
where 1
  and url like '%convenio%';
update click
set url = SUBSTRING_INDEX(url, 'eNort', 1)
where 1
  and url like '%convenio%';
update click
set url = SUBSTRING_INDEX(url, '?', 1)
where 1
  and url like '%convenio%';
update click
set url = SUBSTRING_INDEX(url, '&', 1)
where 1
  and url like '%convenio%';
update click
set url = SUBSTRING_INDEX(url, '\'A', 1)
where 1
  and url like '%\'A%';
update click
set url = replace(url, '/lcb/convenio', '/convenio');
update click
set url = replace(url, '/lcb/profile', '/profile');
update click
set url = replace(url, '/convenio/void/', '/convenio/');
update click
set url = replace(url, '//convenio', '/convenio');

SELECT url
from click
where 1
  and url like '%convenio%';

-- commit;
