-- Users Table
users (
  id,
  name,
  email,
  password,
  role
);

-- Courses Table
courses (
  id,
  course_code,
  course_title,
  credit_unit
);

-- Results Table
results (
  id,
  user_id,
  course_id,
  grade
);

-- Recommendations Table
recommendations (
  id,
  user_id,
  course_id
);