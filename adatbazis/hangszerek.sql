SELECT * FROM instruments;

update instruments Set name = 'árvíztûrõ tükörfúrógép' where id= 1;

# post hangszer
insert into instruments
  (name, description, brand,price, quantity)
  VALUES
  ('Fuvola', 'szépen fütyül', 'adidas', 320000, 20);

delete from instruments where id = 10;

update instruments set
  name ='csigidigi',
  description = 'blabla',
  brand = 'brand',
  price = 2000,
  quantity = 10
where id = 8;