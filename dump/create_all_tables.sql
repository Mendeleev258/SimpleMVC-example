-- Создание всех необходимых таблиц для приложения
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;
SET character_set_connection=utf8mb4;
SET character_set_client=utf8mb4;
SET character_set_results=utf8mb4;

-- Отключение проверки внешних ключей для избежания проблем с порядком создания
SET FOREIGN_KEY_CHECKS = 0;

-- Таблица пользователей
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id smallint unsigned NOT NULL AUTO_INCREMENT,
  login varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  pass varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  timestamp datetime DEFAULT CURRENT_TIMESTAMP,
  email varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  salt int DEFAULT NULL,
  role varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'auth_user',
  PRIMARY KEY (id),
  UNIQUE KEY login (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица категорий
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
  id smallint unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  description text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица подкатегорий
DROP TABLE IF EXISTS subcategories;
CREATE TABLE subcategories (
  id smallint unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  categoryId smallint unsigned NOT NULL,
   
  PRIMARY KEY (id),
  FOREIGN KEY (categoryId) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица заметок
DROP TABLE IF EXISTS notes;
CREATE TABLE notes (
 id int NOT NULL AUTO_INCREMENT,
  publicationDate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  content mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица статей
DROP TABLE IF EXISTS articles;
CREATE TABLE articles (
  id smallint unsigned NOT NULL AUTO_INCREMENT,
  publicationDate date NOT NULL,
  categoryId smallint unsigned NOT NULL,
  subcategoryId smallint unsigned DEFAULT NULL,
  title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  summary text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  content mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  active tinyint NOT NULL DEFAULT '1',
 
  PRIMARY KEY (id),
  FOREIGN KEY (categoryId) REFERENCES categories(id),
  FOREIGN KEY (subcategoryId) REFERENCES subcategories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица для связи статей и авторов
DROP TABLE IF EXISTS article_authors;
CREATE TABLE article_authors (
  article_id smallint unsigned NOT NULL,
  user_id smallint unsigned NOT NULL,
  PRIMARY KEY (article_id, user_id),
  FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Восстановление проверки внешних ключей
SET FOREIGN_KEY_CHECKS = 1;

-- Вставляем тестовые данные
INSERT INTO categories (id, name, description) VALUES 
(1, 'Первый сорт', 'Это первая созданная категория, она была отредактирована после отладки ошибок'),
(3, 'Статьи про preg_replace', 'Здесь будут сохранены факты о функции preg_replace с целью понять, зачем же она понадобилась создателю сайта');

INSERT INTO subcategories (id, name, categoryId) VALUES 
(1, 'шайтан', 1),
(2, 'прегпрег', 3);

INSERT INTO articles (id, publicationDate, categoryId, subcategoryId, title, summary, content, active) VALUES 
(1, '2017-06-20', 1, NULL, 'Первопроходцы ', 'Это статья - первопроходец', 'Первопроходец - человек(или статья), проложивший новые пути, открывший новые земли', 1),
(2, '2017-06-17', 1, 1, 'Неведомые земли', 'Каждый человек хотя бы раз просыпался с утра с будоражащим чувством, что сегодня он не вернётся домой. ', 'Не так сложно отправиться в путь, как решиться на это. Лишь немногие посвятили свою жизнь познанию, изучению тайн нашей планеты. И ещё меньше тех, о ком мы знаем это наверняка. Но несмотря на это, они шли вперёд, и вклад их в общее дело велик. ', 1),
(3, '2017-06-17', 1, 1, 'Х. Колумб', 'Это итальянский мореплаватель, в 1492 году открывший для европейцев Америку, благодаря снаряжению экспедиций католическими королями.', 'Колумб первым из достоверно известных путешественников пересёк Атлантический океан в субтропической и тропической полосе северного полушария и первым из европейцев ходил в Карибском море и Саргассово море [2]. Он открыл и положил начало исследованию Южной и Центральной Америки, включая их континентальные части и близлежащие архипелаги — Большие Антильские (Куба, Гаити, Ямайка и Пуэрто-Рико), Малые Антильские (от Доминики до Виргинских островов, а также Тринидад) и Багамские острова.\r\n\r\nПервооткрывателем Америки Колумба можно назвать с оговорками, ведь ещё в Средние века на территории Северной Америки бывали европейцы в лице исландских викингов (см. Винланд). Но, поскольку за пределами Скандинавии сведений об этих походах не было, именно экспедиции Колумба впервые сделали сведения о землях на западе всеобщим достоянием и положили начало колонизации Америки европейцами.\r\n\r\nВсего Колумб совершил 4 плавания к Америке:\r\n\r\n    Первое плавание (3 августа 1492 — 15 марта 1493).\r\n    Второе плавание (25 сентября 1493 — 11 июня 1496).\r\n    Третье плавание (30 мая 1498 — 25 ноября 1500).\r\n    Четвёртое плавание (9 мая 1502 — 7 ноября 1504).\r\n', 1),
(4, '2017-06-18', 1, NULL, ' В. Янсзон и А.Тасман', ' Голландский мореплаватель и губернатор Виллем Янсзон стал первым европейцем, увидевшим побережье Австралии.', 'Янсзон отправился в своё третье плавание из Нидерландов к Ост-Индии 18 декабря 1603 года в качестве капитана Duyfken, одного из двенадцати судов большого флота Стивена ван дер Хагена (англ.)русск..[113] Уже в Ост-Индии Янсзон получил приказ отправиться на поиски новых торговых возможностей, в том числе в «к большой земле Новой Гвинеи и другим восточным и южным землям.» 18 ноября 1605 года Duyfken вышел из Бантама к западному берегу Новой Гвинеи. Янсзон пересёк восточную часть Арафурского моря, и, не увидев Торресов пролив, вошёл в залив Карпентария. 26 февраля 1606 года он высадился у реки Пеннефазер (англ.)русск. на западном берегу полуострова Кейп-Йорк в Квинсленде, рядом современным городом Уэйпа. Это была первая задокументированная высадка европейцев на австралийский континут. Янсзон нанёс на карту около 320 км побережья, полагая, что это южное продолжение Новой Гвинеи. В 1615 году Якоб Лемер и Виллем Корнелис Схаутен, обойдя мыс Горн, доказали, что Огненная Земля является островом и не может быть северной частью неизвестного южного континента.\r\n\r\nВ 1642—164 годах Абель Тасман, также голландский исследователь и купец на службе VOC, обошёл вокруг Новой Голландии, доказав, что Австралия не является частью мифического южного континента. Он стал первым европейцем, достигшим острова Земля Ван-Димена (сегодня Тасмания) и Новой Зеландии, а также в 1643 году наблюдал острова Фиджи. Тасман, его капитан Вискер и купец Гилсманс также нанесли на карту отдельные участки Австралии, Новой Зеландии и тихоокеанских островов.', 1),
(5, '2017-06-17', 3, 2, 'Description ', 'Выполняет поиск и замену по регулярному выражению  ', ' mixed preg_replace ( mixed $pattern , mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )\r\n\r\nВыполняет поиск совпадений в строке subject с шаблоном pattern и заменяет их на replacement. \r\n\r\n preg_replace() возвращает массив, если параметр subject является массивом, иначе возвращается строка. Если найдены совпадения, возвращается новая версия subject, иначе subject возвращается нетронутым, в случае ошибки возвращается NULL.\r\n\r\nС версии PHP 5.5.0, если передается модификатор \"\\e\", вызывается ошибка уровня E_DEPRECATED. С версии PHP 7.0 в этом случае выдается E_WARNING и сам модификатор игнорируется.\r\n\r\nPHP 7.0: Удалена поддержка модификатора /e. Вместо него используйте preg_replace_callback(). ', 0),
(6, '2017-06-18', 1, NULL, 'С.И. Дежнёв', 'Искони известна тяга русского человека к неизведанным местам. Казак Семен Дежнев первым из европейцев отделил Евразию от Америки, вышел в Тихий океан. Он и его собратья бродили на утлых лодьях по Великому океану вдоль Курильской гряды. Эти люди, их спутники и последователи не искали славы и золота, они были подвижниками, следопытами.', 'Семён Иванович Дежнёв (ок. 1605, Великий Устюг — нач. 1673, Москва) — выдающийся русский мореход, землепроходец, путешественник, исследователь Северной и Восточной Сибири, казачий атаман, а также торговец пушниной, первый из известных европейских мореплавателей, в 1648 году, на 80 лет раньше, чем Витус Беринг, прошёл Берингов пролив, отделяющий Аляску от Чукотки.\r\nПримечательно, что Берингу не удалось пройти весь пролив целиком, а пришлось ограничиться плаванием только в его южной части, тогда как Дежнёв прошёл пролив с севера на юг, по всей его длине.\r\nЗа 40 лет пребывания в Сибири Дежнев участвовал в многочисленных боях и стычках, имел не менее 13 ранений, включая три тяжелых. Судя по письменным свидетельствам, его отличали надежность, честность и миролюбие, стремление исполнить дело без кровопролития.\r\nИменем Дежнева названы мыс, остров, бухта, полуостров и село. В центре Великого Устюга в 1972 году ему установлен памятник.', 1);

-- Добавляем пользователей
INSERT INTO users (id, login, pass, role) VALUES
(1, 'mendel', '$2y$10$GRJPxVFMiAVQa.IZfs7eG.XdLn7C7MLLKTXbWh21QdwRfhhrAdN5C', 'auth_user'),
(2, 'mandaley', '$2y$10$f5Ci8YduaqEMY7T2GUVBSO53CX/KK2QXUR2C7o7ofJFCM053/pTm.', 'auth_user'),
(3, 'lesya_kochkina', '$2y$10$uXXGP3bgX.YJasVeUI8RkOJx8WDRug5PKxfDY/Y7cvD4nFnD91TUu', 'auth_user'),
(4, 'admin', '$2y$10$D43pEC8/ZH2UgqsrCBri7u7lF9LzHrP2A5p8Pp2k2k2k2k2k2', 'admin');

-- Связываем статьи и авторов
INSERT INTO article_authors (article_id, user_id) VALUES 
(2, 1),
(4, 1),
(4, 3);

-- Вставляем тестовые заметки
INSERT INTO notes (id, publicationDate, title, content) VALUES 
(2, '2021-02-01 00:00', 'New page', 'New year, new page, new note!'),
(4, '2021-02-03 00:00:00', 'New year!', 'С Новым годом!');