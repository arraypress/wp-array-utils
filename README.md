# WordPress Array Utils - Essential Array Operations

A lean PHP library focused on the most commonly needed array operations in WordPress development. Simple, predictable methods that you'll reach for daily.

## Features

* 🎯 **Daily Essentials**: Only the array operations you actually use regularly
* 📍 **Dot Notation**: Access nested array values with simple dot syntax
* 🔍 **Smart Filtering**: Keep or exclude keys with clean syntax
* 📊 **Data Processing**: Group, pluck, and sort arrays efficiently
* 🔗 **Array Comparison**: Check matches between arrays easily
* 🎛️ **WordPress Ready**: Convert arrays for select fields and forms
* ⚡ **Lean & Fast**: Focused on practical operations without bloat
* 🔒 **Safe Operations**: Graceful handling of missing keys and invalid data

## Requirements

* PHP 7.4 or later

## Installation

```bash
composer require arraypress/wp-array-utils
```

## Basic Usage

### Array Manipulation

```php
use ArrayPress\ArrayUtils\Arr;

// Get specific values
$first = Arr::first(['a', 'b', 'c']); // 'a'
$last = Arr::last(['a', 'b', 'c']); // 'c'

// Filter arrays by keys
$allowed = Arr::only($user_data, ['name', 'email']); // Keep only these keys
$clean = Arr::except($user_data, ['password', 'secret']); // Remove these keys

// Sort arrays (with absint filtering for numeric)
$sorted = Arr::sort_numeric([3, 1, 4, 1, 5]); // [1, 1, 3, 4, 5]
$alphabetical = Arr::sort_alphabetic(['zebra', 'apple', 'banana']);
$by_column = Arr::sort_by_column($users, 'name'); // Sort by name column
$by_key = Arr::sort_by_key(['zebra' => 1, 'apple' => 2]); // Sort by array keys
```

### Dot Notation Access

```php
// Access nested data easily
$config = [
    'database' => [
        'connections' => [
            'mysql' => ['host' => 'localhost']
        ]
    ]
];

$host = Arr::get($config, 'database.connections.mysql.host'); // 'localhost'
$port = Arr::get($config, 'database.connections.mysql.port', 3306); // 3306 (default)

// Set nested values
$config = Arr::set($config, 'database.connections.mysql.port', 3307);

// Check if nested key exists
if (Arr::has($config, 'database.connections.redis')) {
    // Redis connection exists
}
```

### Grouping and Aggregation

```php
$users = [
    ['name' => 'John', 'role' => 'admin', 'status' => 'active'],
    ['name' => 'Jane', 'role' => 'user', 'status' => 'active'],
    ['name' => 'Bob', 'role' => 'admin', 'status' => 'inactive']
];

// Group by role
$by_role = Arr::group_by($users, 'role');
// ['admin' => [...], 'user' => [...]]

// Pluck specific values
$names = Arr::pluck($users, 'name'); // ['John', 'Jane', 'Bob']

// Flatten nested arrays
$nested = [
    'fruits' => ['apple', 'banana'],
    'colors' => ['red', 'blue']
];
$flat = Arr::flatten($nested); // ['apple', 'banana', 'red', 'blue']
```

### Array Manipulation

```php
$menu = ['home' => 'Home', 'contact' => 'Contact'];

// Insert after specific key
$menu = Arr::insert_after($menu, 'home', ['about' => 'About']);
// ['home' => 'Home', 'about' => 'About', 'contact' => 'Contact']

// Insert before specific key
$menu = Arr::insert_before($menu, 'contact', ['services' => 'Services']);

// Shuffle array
$shuffled = Arr::shuffle([1, 2, 3, 4, 5]);
```

### Array Comparison (Common Patterns)

```php
// Check if all elements in one array exist in another
$permissions = ['read', 'write', 'delete'];
$user_perms = ['read', 'write'];
$has_all = Arr::has_all_matches($user_perms, $permissions); // true

// Check if any elements match between arrays
$arr1 = ['apple', 'banana'];
$arr2 = ['banana', 'cherry'];  
$has_any = Arr::has_any_matches($arr1, $arr2); // true (banana matches)

// Common WordPress use case - checking post capabilities
$required_caps = ['edit_posts', 'delete_posts'];
$user_caps = ['edit_posts', 'delete_posts', 'manage_options'];
$can_do_all = Arr::has_all_matches($required_caps, $user_caps); // true
```

### WordPress Select Field Integration

```php
// Convert regular array to select field options format
$post_types = ['post' => 'Posts', 'page' => 'Pages', 'product' => 'Products'];
$options = Arr::to_options($post_types);
// [
//   ['value' => 'post', 'label' => 'Posts'],
//   ['value' => 'page', 'label' => 'Pages'],
//   ['value' => 'product', 'label' => 'Products']
// ]

// Convert back from options format
$original = Arr::from_options($options);
// ['post' => 'Posts', 'page' => 'Pages', 'product' => 'Products']
```

### Format Conversion

```php
// Convert to string format
$tags = ['wordpress', 'php', 'javascript'];
$string = Arr::to_string($tags); // 'wordpress,php,javascript'
$string = Arr::to_string($tags, ' | '); // 'wordpress | php | javascript'
$quoted = Arr::to_string($tags, ',', '"'); // '"wordpress","php","javascript"'
```

## Use Cases

### WordPress Post Processing

```php
function process_posts($posts) {
    // Group posts by status
    $by_status = Arr::group_by($posts, 'post_status');
    
    // Get all post IDs for bulk operations
    $post_ids = Arr::pluck($posts, 'ID');
    
    // Sort posts by title
    $sorted = Arr::sort_by_column($posts, 'post_title');
    
    return [
        'by_status' => $by_status,
        'post_ids' => $post_ids,
        'sorted' => $sorted
    ];
}
```

### Settings Management

```php
function get_nested_setting($settings, $path, $default = null) {
    return Arr::get($settings, $path, $default);
}

// Usage
$host = get_nested_setting($config, 'database.mysql.host', 'localhost');
$debug = get_nested_setting($config, 'app.debug', false);
```

### Permission Checking

```php
function user_can_perform_actions($user_capabilities, $required_actions) {
    return Arr::has_all_matches($required_actions, $user_capabilities);
}

// Usage
$required = ['edit_posts', 'delete_posts'];
$user_caps = ['edit_posts', 'delete_posts', 'manage_options'];
$can_edit = user_can_perform_actions($user_caps, $required); // true
```

### Menu Management

```php
function add_menu_item_after($menu, $after_key, $new_item) {
    return Arr::insert_after($menu, $after_key, $new_item);
}

// Usage - add "About" after "Home" in navigation
$menu = ['home' => 'Home', 'services' => 'Services', 'contact' => 'Contact'];
$menu = add_menu_item_after($menu, 'home', ['about' => 'About Us']);
```

## API Reference

### Arr Class

**Selection:**
- `first(array $array)` - Get first element
- `last(array $array)` - Get last element

**Filtering:**
- `only(array $array, array $keys)` - Keep only specified keys
- `except(array $array, array $keys)` - Remove specified keys

**Dot Notation:**
- `get(array $array, string $path, $default = null)` - Get nested value
- `set(array $array, string $path, $value)` - Set nested value
- `has(array $array, string $path)` - Check if nested key exists

**Sorting:**
- `sort_numeric(array $array, bool $desc = false)` - Sort numeric values (with absint)
- `sort_alphabetic(array $array, bool $desc = false)` - Sort alphabetically
- `sort_by_key(array $array, bool $desc = false)` - Sort by array keys
- `sort_by_column(array $array, string $key, bool $desc = false)` - Sort by column

**Data Processing:**
- `group_by(array $array, string $key)` - Group by key value
- `pluck(array $array, string $key)` - Extract column values
- `flatten(array $array, bool $unique = false)` - Flatten multidimensional arrays

**Array Manipulation:**
- `insert_after(array $array, string $key, array $new)` - Insert after key
- `insert_before(array $array, string $key, array $new)` - Insert before key
- `shuffle(array $array)` - Shuffle array elements

**Array Comparison:**
- `has_all_matches(array $array1, array $array2)` - Check if all elements in array1 exist in array2
- `has_any_matches(array $array1, array $array2)` - Check if any elements match

**Conversion:**
- `to_string(array $array, string $delimiter = ',')` - Convert to delimited string

**WordPress Helpers:**
- `to_options(array $array)` - Convert to select field format
- `from_options(array $options)` - Convert from select field format

## Error Handling

All methods return sensible defaults for invalid inputs rather than throwing exceptions:
- Missing array keys return provided defaults
- Empty arrays return null or empty arrays as appropriate
- Invalid operations return the original array unchanged

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-array-utils)
- [Issue Tracker](https://github.com/arraypress/wp-array-utils/issues)