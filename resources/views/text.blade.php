SELECT title, content
FROM chatbot_knowledge
WHERE title LIKE CONCAT('%', {{$json["question"]}}, '%')
   OR content LIKE CONCAT('%', {{$json["question"]}}, '%')
LIMIT 5;
