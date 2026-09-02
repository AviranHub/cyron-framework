# Cyron Activity Tracking

Applications emit their own domain events:

    ActivityTracker::record('book.opened', ['book_id' => $book->id], null, 'باز کردن کتاب');
    ActivityTracker::record('order.completed', ['order_id' => $order->id, 'total' => $total]);
    ActivityTracker::record('course.started', ['course_id' => $course->id]);

Event names use category.action and application-specific data belongs in properties. Cyron does not hard-code commerce or other business modules; developers can build their own domains while using the shared Admin analytics pipeline.

## Event Registry

Define event metadata once in config/analytics.php:

    EventRegistry::registerMany(['book.opened' => ['label' => 'باز کردن کتاب', 'category' => 'reading']]);

Then record only the event and properties. Applications may register any domain-specific event.
