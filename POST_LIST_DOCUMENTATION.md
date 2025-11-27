# Post List View Documentation

## Overview

`post_list.php` is the main view file for the RSS Scheduler application's post management interface. It displays a paginated list of imported RSS posts with the ability to:

- View and manage post priorities via drag-and-drop
- Assign posts to social media platforms (with validation)
- Edit and delete posts
- Filter and paginate through large numbers of posts

**Location:** `application/views/post_list.php`

---

## Page Structure

### 1. Navigation Bar
- **Brand Link:** Returns to RSS import page (`/rss`)
- **Links:**
  - "Posts" - Current page (active)
  - "Dashboard" - Platform analytics view
- **Responsive:** Collapses on mobile devices

### 2. Page Header
- Title: "Posts"
- Action Buttons:
  - "Import New RSS" - Navigate to RSS import form
  - "Social Dashboard" - View social media dashboard

### 3. Main Content Area

#### Empty State
If no posts exist, displays an informational alert suggesting the user import an RSS feed.

#### Posts Table
Displays posts with the following columns:

| Column | Width | Content |
|--------|-------|---------|
| Priority | 100px | Drag handle icon (☰) with priority badge |
| Title | Auto | Post title + publication date |
| Char Count | 130px | Character count badge |
| Social Platforms | 260px | Assigned platforms (comma-separated) |
| Actions | 140px | Edit and Delete buttons |

#### Pagination
- Shows previous/next buttons and page numbers
- Only displays if total pages > 1
- Links to specific pages

---

## Data Attributes

Each table row contains data attributes for JavaScript functionality:

```php
<tr class="draggable-row"
    draggable="true"
    data-id="<?= $p->id ?>"
    data-platforms="<?= $platformAttr ?>"
    data-charcount="<?= $p->char_count ?>">
```

| Attribute | Purpose |
|-----------|---------|
| `data-id` | Unique post identifier |
| `data-platforms` | Comma-separated list of assigned platform IDs |
| `data-charcount` | Total character count for validation |

---

## Edit Modal

### Purpose
Allows users to assign social media platforms to posts with validation.

### Fields
- **Priority** (disabled) - Display only
- **Title** (disabled) - Display only
- **Social Platforms** - Checkboxes for each available platform

### Validation
- **X (Twitter) Character Limit:** Posts assigned to X must be ≤ 280 characters
- Error message displays if limit is exceeded
- Form submission is blocked if validation fails

---

## PHP Variables

The following variables must be passed to this view from the controller:

| Variable | Type | Description |
|----------|------|-------------|
| `$posts` | Array of objects | Posts to display (paginated) |
| `$platforms` | Array of objects | Available platforms for assignment |
| `$current_page` | Integer | Current page number (1-indexed) |
| `$total_pages` | Integer | Total number of pages |

### Post Object Structure
```php
{
    id: (int),                  // Unique post identifier
    title: (string),            // Post title
    content: (string),          // Post content
    pub_date: (string),         // Publication date (Y-m-d H:i:s)
    char_count: (int),          // Character count of title + content
    priority: (int),            // Display priority (1 = highest)
    platform_ids: array(string) // Assigned platform IDs
}
```

### Platform Object Structure
```php
{
    id: (int),    // Unique platform identifier
    name: (string) // Platform name (e.g., "X", "Facebook", "LinkedIn")
}
```

---

## JavaScript Functionality

### 1. Drag & Drop Priority System

**File:** Lines 328-385

**How it works:**
1. Table rows have `draggable="true"` attribute
2. User clicks and holds a row to initiate drag
3. Hovering over other rows reorders them in the DOM
4. Dropping the row triggers a server update

**Key Events:**
- `dragstart` - Marks row as being dragged
- `dragend` - Clears drag state
- `dragover` - Handles visual reordering
- `drop` - Sends priority update to server

**Server Interaction:**
- Endpoint: `POST /rss/update_priority`
- Parameters:
  - `id` - Post ID
  - `priority` - New priority number (1-based)
- Response: JSON with `status` field ("ok" or "error")

**Visual Feedback:**
- Dragged rows have 60% opacity
- Cursor changes to "move" on hover

---

### 2. Edit Modal System

**File:** Lines 387-443

**Modal Components:**
- Modal ID: `editModal`
- Form ID: `editForm`
- Uses Bootstrap 5 Modal API

**Initialization:**
```javascript
const editModal = new bootstrap.Modal(editModalEl);
```

**Opening the Modal:**
- Click "Edit" button on any post row
- Modal populates with post data from the row's DOM elements
- Platform checkboxes are pre-checked based on current assignments

**Data Collection:**
```javascript
// Extracted from row data attributes
id        = row.dataset.id
priority  = row.querySelector('.priority-text').textContent
title     = row.querySelector('.post-title-text').textContent
charCount = parseInt(row.dataset.charcount)
platformIds = row.dataset.platforms.split(',').filter(Boolean)
```

---

### 3. Platform Validation

**File:** Lines 402-419

**Function:** `validateXLength()`

**Purpose:** Ensures posts assigned to X (Twitter) don't exceed 280 characters

**Validation Logic:**
1. Check if X platform exists (`X_PLATFORM_ID`)
2. Check if X checkbox is selected
3. Compare `currentEditCharCount` with 280-character limit
4. Display/hide error message based on result
5. Returns boolean for form submission control

**Error Message Template:**
```
"This post has [COUNT] characters, which exceeds the 280-character limit for X."
```

**When Triggered:**
- On X checkbox change
- On form submission (blocks submit if invalid)

---

### 4. Form Submission

**File:** Lines 428-443

**Flow:**
1. Validate using `validateXLength()`
2. Collect all checked platform IDs
3. Build FormData with post_id and platforms array
4. Send POST request to `/rss/assign_platform`
5. Update UI if successful
6. Close modal and display error if failed

**Server Interaction:**
- Endpoint: `POST /rss/assign_platform`
- Parameters:
  - `post_id` - Post ID
  - `platforms[]` - Array of selected platform IDs
- Response: JSON with `status` field

**UI Update on Success:**
- Updates row's `data-platforms` attribute
- Updates `.platforms-text` cell with new platform names
- Closes the modal

---

## Bootstrap Dependencies

The view uses Bootstrap 5.3.3 for styling:

- **Grid System:** Responsive containers and columns
- **Navigation:** Navbar component
- **Tables:** Table, responsive wrapper, badges
- **Pagination:** Custom pagination styling
- **Modal:** Form modal for editing
- **Buttons:** Various button styles (primary, outline, danger)
- **Forms:** Input, checkbox, textarea components
- **Alerts:** Info alert for empty state
- **Utilities:** Spacing, text alignment, display classes

**CDN Links:**
- CSS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
- JS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js`

---

## Custom CSS

```css
tr.draggable-row.dragging { 
    opacity: 0.6; 
}
tr.draggable-row { 
    cursor: move; 
}
```

**Purpose:** Visual feedback for draggable rows

---

## Security Considerations

### XSS Prevention
- `htmlspecialchars()` escapes user-generated content (titles, platform names)
- Applied to:
  - Post titles
  - Platform names in display text
  - All dynamic content output

### CSRF Protection
- POST requests use standard CodeIgniter form handling
- FormData method prevents accidental browser caching

### Authorization
- No authentication checks in view (handled by controller)
- Assume user is already authenticated before reaching this view

---

## Dependencies

### Server-Side
- **Controller:** `Rss.php` - Routes requests and provides data
- **Model:** `Rss_model.php` - Database operations
- **Database Tables:**
  - `posts` - Main post records
  - `platforms` - Available social platforms
  - `post_platforms` - Junction table for post-platform relationships

### Client-Side
- Bootstrap 5.3.3
- Modern browser support for:
  - Fetch API (ES6)
  - DOM drag-and-drop events
  - Template literals
  - Array methods (map, filter, forEach)

---

## URL Routes

All links and fetch calls use CodeIgniter's `base_url()` helper function:

| Route | Method | Purpose |
|-------|--------|---------|
| `/rss` | GET | Import RSS feed page |
| `/rss/posts` | GET | View posts (current page) |
| `/rss/posts/[page]` | GET | View specific page |
| `/rss/dashboard` | GET | View analytics dashboard |
| `/rss/social-dashboard` | GET | View social media dashboard |
| `/rss/delete/[id]` | GET | Delete post (with confirmation) |
| `/rss/update_priority` | POST | Update post priority via drag-drop |
| `/rss/assign_platform` | POST | Assign platforms to post |

---

## Usage Flow

### Typical User Journey

1. **View Posts:** User navigates to `/rss/posts`
   - Controller loads posts with pagination
   - View renders table with all posts

2. **Reorder Priorities:** User drags a row
   - Row moves to new position
   - Priority numbers update immediately
   - Fetch request updates database
   - User sees success/error feedback

3. **Edit Platforms:** User clicks "Edit" button
   - Modal opens with post details
   - User checks/unchecks platform checkboxes
   - If X is selected and post > 280 chars, error shows
   - User clicks "Save Changes"
   - Form submits via Fetch API
   - UI updates with new platforms
   - Modal closes

4. **Delete Post:** User clicks "Delete" button
   - Confirmation dialog appears
   - If confirmed, page redirects to delete endpoint
   - Server deletes post and adjusts priorities
   - Page reloads showing updated posts

---

## Performance Considerations

### Pagination
- **Default Limit:** 10 posts per page
- **Benefit:** Reduces DOM elements and improves initial load time
- **Handled by:** Controller (`Rss.php` lines 113-121)

### Drag-and-Drop
- Uses native HTML5 drag events (no jQuery required)
- Minimal DOM manipulation during drag
- Throttled by drop event (only sends one update)

### Fetch API
- Uses modern Fetch API instead of AJAX libraries
- Lightweight, native browser API
- Error handling for network failures

---

## Accessibility Notes

- Form labels properly associated with inputs
- Modal has ARIA attributes (`aria-hidden`, `aria-label`)
- Buttons have descriptive text or ARIA labels
- Table structure with `<thead>` and `<tbody>`
- Semantic HTML for navigation (`<nav>`, `<ul>`, `<li>`)
- Color contrast meets WCAG standards

---

## Common Issues & Troubleshooting

### Issue: Drag-and-drop not working
**Possible Causes:**
- JavaScript error in console
- `posts-tbody` element not found in DOM
- Browser doesn't support HTML5 drag-drop (unlikely)

**Solution:**
- Check browser console for errors
- Verify posts are being rendered (not empty state)
- Test in modern browser (Chrome, Firefox, Safari, Edge)

### Issue: Modal not appearing when clicking Edit
**Possible Causes:**
- Bootstrap modal JavaScript not loaded
- DOM element with class `btn-edit` not found
- JavaScript error in modal initialization

**Solution:**
- Verify Bootstrap JS CDN is loaded
- Check that posts are rendering
- Look for errors in browser console

### Issue: X platform validation not working
**Possible Causes:**
- `X_PLATFORM_ID` is null (X platform not configured)
- JavaScript error in validation function
- Character count not properly passed

**Solution:**
- Verify X platform exists in database
- Check that `char_count` is a valid integer
- Inspect JavaScript console for errors

### Issue: Platform changes not saving
**Possible Causes:**
- Server endpoint not responding
- Network error
- Form data not properly formatted
- Server-side validation failing

**Solution:**
- Check network tab in browser DevTools
- Verify `/rss/assign_platform` endpoint exists in controller
- Check server error logs
- Ensure platform IDs are valid integers

---

## Future Enhancement Ideas

1. **Bulk Actions:** Select multiple posts for batch operations
2. **Search/Filter:** Find posts by title or content
3. **Advanced Sorting:** Sort by date, character count, platforms
4. **Post Preview:** Modal showing full post content before deletion
5. **Export:** Download posts as CSV or JSON
6. **Scheduling:** Schedule posts for automatic publishing
7. **Templates:** Save post templates for quick reuse
8. **Tags/Categories:** Organize posts with custom tags
9. **Undo/Redo:** Undo recent changes (priority, deletions)
10. **Real-time Updates:** WebSocket support for multi-user editing

---

## Version History

- **v1.0** - Initial release with drag-and-drop priorities and platform assignment

---

## Related Files

- `application/controllers/Rss.php` - Request handler
- `application/models/Rss_model.php` - Database operations
- `application/views/rss_import.php` - Import RSS feed form
- `application/views/dashboard.php` - Platform analytics
- `application/views/social_dashboard.php` - Social media view

---

## Contact & Support

For issues or questions regarding this view, please refer to:
- CodeIgniter Documentation: https://codeigniter.com/docs
- GitHub Issues: Check project repository
- Community Forums: https://forum.codeigniter.com/
