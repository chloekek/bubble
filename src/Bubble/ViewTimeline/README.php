<?php
/**
 * @defgroup view_timeline View timeline
 *
 * @brief The “view timeline” use case displays the posts in a timeline.
 *
 * For each bubble there is a timeline that displays the posts in the bubble.
 * The special “all” timeline displays the posts in the union of all bubbles.
 * The universe of bubbles, in this case, is the set of bubbles that belong to
 * the user. Posts are displayed in reverse chronological order: more recent
 * posts are listed first.
 *
 * Timelines are not materialized anywhere; they are derived from bubbles. In
 * database terms, a timeline is a query.
 */
namespace Bubble\ViewTimeline;
