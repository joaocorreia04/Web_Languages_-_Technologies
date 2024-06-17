PRAGMA foreign_keys=ON;

DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS purchase;
DROP TABLE IF EXISTS item;
DROP TABLE IF EXISTS color;
DROP TABLE IF EXISTS brand;
DROP TABLE IF EXISTS users;


CREATE TABLE item (
    item_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(25) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) CHECK (price > 0) NOT NULL,
    description VARCHAR(512),
    size VARCHAR(10), 
    condition VARCHAR(20),
    category VARCHAR(30),
    sub_category VARCHAR(30),
    color_id INT REFERENCES color(color_id) ON DELETE SET NULL ON UPDATE CASCADE,
    brand_id INT REFERENCES brand(brand_id) ON DELETE SET NULL ON UPDATE CASCADE,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT condition_check CHECK (condition IS NULL OR condition IN ('Great', 'Good', 'Bad', 'Excellent')),
    CONSTRAINT category_check CHECK (category IS NULL OR category IN ('Men', 'Women', 'Children')),
    CONSTRAINT sub_category_check CHECK (
        (sub_category is NULL) OR
        (category = 'Men' AND sub_category IN ('Jeans', 'T-Shirts', 'Shirts', 'Pants')) OR
        (category = 'Women' AND sub_category IN ('Jeans', 'Tops', 'Dresses', 'Skirts')) OR
        (category = 'Children' AND sub_category IN ('Jeans', 'T-Shirts', 'Dresses', 'Pants'))
    ),
    CONSTRAINT size_check CHECK (
        (category <> 'Children' AND size IN ('XS', 'S', 'M', 'L', 'XL', 'XXL')) OR
        (category = 'Children' AND size IN('Baby','3-4','5-6','7-8','9-10','11-12')) 
    )
);

--username is treated as the name the user wants to be called in our platform,he can also choose to edit it later on
CREATE TABLE users (
    username VARCHAR(25) PRIMARY KEY,
    password VARCHAR(25) NOT NULL,
    email VARCHAR(25) NOT NULL,
    phone_number VARCHAR(15),
    seller_rating INT CONSTRAINT rate_seller CHECK (seller_rating <= 5 AND seller_rating >= 0),
    buyer_rating INT CONSTRAINT rate_buyer CHECK (buyer_rating <= 5 AND buyer_rating >= 0),
    admin_state VARCHAR(6) CONSTRAINT is_admin CHECK (admin_state = 'true' OR admin_state = 'false'),
    profile_img_url VARCHAR(255) DEFAULT '../uploads/profile_default.png',
    profile_background_img_url VARCHAR(255) DEFAULT '/uploads/background_default.jpg',
    is_admin INTEGER DEFAULT 0 CHECK (is_admin IN (0, 1))
);



CREATE TABLE wishlist(
    wishlist_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(25) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    item_id INT REFERENCES item (item_id) ON DELETE CASCADE ON UPDATE CASCADE
    
 );

CREATE TABLE message ( 
    message_id INTEGER PRIMARY KEY AUTOINCREMENT,
    from_id VARCHAR(25) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
    to_id VARCHAR(25) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
    content  TEXT ,
    image_url VARCHAR(255),
    date TIMESTAMP NOT NULL default CURRENT_TIMESTAMP
);

CREATE TABLE purchase (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    buyer VARCHAR(25) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
    item_id INT REFERENCES item(item_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
    state VARCHAR(12),
    CONSTRAINT state_check CHECK (state IN ('Purchased', 'Completed'))
);

/*INSERTS cant insert users because of hash password will make login impossible*/
