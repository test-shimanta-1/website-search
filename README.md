# Website Search

Website Search is a lightweight WordPress plugin that enables **global search across your entire website**, including posts, pages, and custom post types.  
You can easily add a search form or a modal-based search anywhere on your site using a simple shortcode.

---

## Features

- Global search across posts, pages, and custom post types
- Clean search URLs (`/search/?q=keyword`)
- Shortcode-based integration
- Optional modal (popup) search interface
- Customizable modal button text
- suport of wordpress pagination on display search results.

---

## Installation

1. Download or clone this repository.
2. Upload the `website-search` folder to `/wp-content/plugins/`.
3. Activate **Website Search** from the WordPress admin dashboard.
4. Go to **Settings → Permalinks** and click **Save Changes** (required once).

---

## Shortcode Usage

### Display a simple search form

Use this shortcode to display a basic search form anywhere on your site:

```text
[website_search]
```

### Display a modal (popup) search form

Use this shortcode to show a button that opens the search form in a modal popup:

```text
[website_search variant="modal"]
```

### Display a modal (popup) search form

You can customize the modal trigger button text using the button_text attribute:

```text
[website_search variant="modal" button_text="Find Content"]
```