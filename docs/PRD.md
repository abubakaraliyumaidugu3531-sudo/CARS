# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Course Advisory and Recommendation System

---

## 1. 📌 Product Title

**Course Advisory and Recommendation System (CARS)**

---

## 2. 🧭 Product Overview

The Course Advisory and Recommendation System is a web-based application that helps students select appropriate courses based on their academic records, program requirements, and personal interests.

The system automates the traditional academic advising process by providing intelligent course suggestions. This improves decision-making and reduces reliance on manual advising sessions.

---

## 3. 🚨 Problem Statement

The current course advisory process in universities is manual and inefficient, which leads to:

- Limited access to academic advisors due to high student volume
- Lack of personalized course recommendations
- Poor understanding of course prerequisites
- Incorrect course combinations
- Delayed graduation and academic difficulties

There is no automated system to effectively guide students in course selection.

---

## 4. 🎯 Product Goals & Objectives

### 🎯 Goal

Design and implement a system that provides automated, accurate, and personalized course recommendations.

### ✅ Objectives

- Analyze the existing manual advisory system
- Store and manage student academic records
- Develop a recommendation engine for course suggestions
- Provide an easy-to-use interface for users
- Improve the efficiency and accuracy of course selection

---

## 5. 👥 Target Users

- **Students** → Primary users who select courses
- **Academic Advisors** → Provide guidance and review recommendations
- **Administrators** → Manage system data and operations

---

## 6. ⚙️ Key Features

### 6.1 Student Features

- Account registration and login
- Profile setup (department, level, interests)
- View academic records
- Receive course recommendations
- View course details (prerequisites, description)

---

### 6.2 Recommendation Engine

- Suggest courses based on:
    - Completed courses
    - Grades (academic performance)
    - Program requirements
    - Course prerequisites
- Rule-based decision system (initial version)

---

### 6.3 Admin Features

- Add/Edit/Delete courses
- Manage student and advisor accounts
- Input course prerequisites
- Monitor system usage

---

### 6.4 Advisor Features (Optional but Strong)

- Access student profiles
- Review recommendations
- Provide manual adjustments

---

## 7. 🧠 Functional Requirements

The system shall:

- Allow users to register and log in securely
- Store student academic records
- Maintain a database of courses and prerequisites
- Automatically generate course recommendations
- Display course information clearly
- Allow administrators to manage system data
- Allow users to update their profiles

---

## 8. ⚡ Non-Functional Requirements

- **Usability:** Simple and intuitive interface
- **Performance:** Recommendations generated quickly (≤ 3 seconds)
- **Security:** Authentication and data protection
- **Reliability:** Minimal system downtime
- **Scalability:** Support an increasing number of users

---

## 9. 🧩 System Scope

### Included:

- Web-based system
- Student management
- Course management
- Recommendation functionality

### Not Included (for now):

- Full AI/ML recommendation system
- Integration with university live database
- Mobile application

---

## 10. 🚧 Constraints

- Limited real student data
- Time constraints (academic project)
- Rule-based recommendation approach
- May be limited to one department initially

---

## 11. ⚠️ Limitations

- Recommendations depend on the accuracy of available data
- Limited intelligence (not fully AI-driven yet)
- May not cover all faculties in the initial version

---

## 12. 📊 Success Criteria

The system will be considered successful if:

- Students can easily select appropriate courses
- Incorrect course registration is reduced
- Advisors’ workload is reduced
- Users find the system easy to use
- Recommendations align with academic requirements

---

## 13. 🔄 User Flow

1. User registers/logs in
2. User inputs academic details
3. System stores academic records
4. System analyzes prerequisites and performance
5. System recommends courses
6. User reviews and selects courses

---

## 14. 🚀 Future Improvements

- Machine learning-based recommendations
- Multi-department/faculty support
- Mobile application
- Integration with the school portal
- Career path guidance system

---

## 🔥 Important Note for Your Project Defense

If your supervisor asks:

👉 *“How is your PRD different from Chapter One?”*

You should answer:

- Chapter One = **Academic explanation (theory & justification)**
- PRD = **Technical blueprint for building the system**