# CARS Admin Guide

## Overview
The Course Advisory & Recommendation System (CARS) is designed for three user roles:
- **Students**: Register for courses and receive recommendations
- **Advisors**: Review student course plans and provide approval
- **Administrators**: Manage the entire system

This guide covers administrator tasks and features.

## Admin Dashboard
The main admin dashboard shows:
- **System Overview**: Total counts of students, advisors, courses, and recommendation acceptance rates
- **Quick Actions**: Links to key management pages
- **Recent Users**: Latest user registrations

## User Management (`/admin/users`)

### Create Staff Accounts
Create advisor or admin accounts (students self-register):
1. Navigate to **Users** page
2. Fill in staff account details:
   - Name
   - Email
   - Secure password (6+ characters)
   - Role (Advisor or Admin)
   - Department (optional)
3. Click **Create account**

### Search & Filter Users
Find users quickly:
1. Use the search field to find by name or email
2. Filter by role (All Roles, Student, Advisor, Admin)
3. Click **Filter** to apply, or **Clear** to reset

### Edit User Details
Modify user information:
1. Click **Edit** on any user row
2. Update details:
   - Name
   - Email
   - Role
   - Department
   - Academic Level
3. Click **Save Changes**

### Delete Users
Remove user accounts (non-reversible):
1. Click **Delete** on the user row
2. Review warning about cascading deletions
3. Confirm deletion
- All related data (grades, registrations, recommendations) will be deleted
- **You cannot delete your own account** as a safety measure

## Course Management (`/admin/courses`)

### Add Courses
Create new courses in the catalog:
1. Navigate to **Manage Courses**
2. Fill in course details:
   - **Code**: Course identifier (e.g., CSC201)
   - **Title**: Full course name
   - **Credit Units**: Number of credits (1-9)
   - **Department**: Department offering course
   - **Level**: Study level (100, 200, 300, 400)
   - **Semester**: First, Second, or Any
   - **Core Course**: Check if mandatory in program
   - **Description**: Optional course description
3. Click **Add course**

### Search & Filter Courses
Find courses quickly:
1. Search by course code or title
2. Filter by department
3. Click **Filter** or **Clear** to reset

### Edit Courses
Modify course information:
1. Click **Edit** on a course row
2. Update course details
3. Click **Save changes**

### Delete Courses
Remove courses (non-reversible):
1. Click **Delete** on course row
2. Confirm deletion warning
- All prerequisites and academic records linked to this course will be deleted

## Prerequisites Management (`/admin/prerequisites`)

### Add Prerequisites
Define course requirements:
1. Navigate to **Course Prerequisites**
2. Select the course that has a requirement
3. Select the prerequisite course (what must be passed first)
4. Click **Add prerequisite**

### Search Prerequisites
Find prerequisite relationships:
1. Search by course code
2. Filters show all defined prerequisites

### Remove Prerequisites
Delete requirement relationships:
1. Click **Remove** on any prerequisite row
2. Confirm removal
- Only removes the requirement, not the courses themselves

## Academic Records (`/admin/records`)

### Record Student Grades
Enter grades for students:
1. Navigate to **Enter Grades**
2. Select a student
3. Select a course
4. Enter semester and grade (A-F)
5. Click **Save grade**
- Pass/fail status is determined automatically
- Grade points are calculated based on grade scale

### View Student Transcripts
Review student academic history:
1. Select a student from the dropdown
2. View their complete transcript including:
   - Course code and title
   - Semester taken
   - Grade received
   - Pass/fail status

## Reports & Analytics (`/admin/reports`)

### System Statistics
Monitor key metrics:
- **User Counts**: Total students, advisors, admins
- **Course Management**: Total courses in catalog
- **Student to Advisor Ratio**: Workload indicator

### Recommendation Analytics
Track recommendation system effectiveness:
- **Acceptance Rate**: % of recommendations students act on
- **Total Generated**: Total recommendations created
- **Pending Recommendations**: Waiting on student decision

### Prerequisite Compliance
Monitor course requirement adherence:
- **Compliance Rate**: % of registrations meeting prerequisites
- **Compliant Registrations**: Count of valid course selections
- **Total Registrations**: All course registrations

### Coverage Analysis
See how many students benefit from system:
- **Coverage Rate**: % of students with recommendations
- **Covered Students**: Count of students with recommendations
- **Total Students**: All enrolled students

### System Health Insights
Key takeaways:
- Student-to-advisor ratio for workload management
- Average students per advisor
- Total recommendations in system
- Recommendation acceptance patterns
- Prerequisite compliance trends

## Best Practices

### User Management
✓ Review user list periodically for inactive accounts
✓ Maintain appropriate advisor-to-student ratio (recommended 1:20)
✓ Create admin accounts with strong, unique passwords
✓ Document user creations for audit purposes

### Course Management
✓ Keep course catalog up-to-date with actual offerings
✓ Set accurate course levels and semesters
✓ Mark core (mandatory) courses correctly
✓ Define all course prerequisites to ensure prerequisite compliance

### Prerequisite Management
✓ Review prerequisites when adding new courses
✓ Update prerequisites when curriculum changes
✓ Validate prerequisite chains (avoid circular dependencies)
✓ Ensure passed courses are properly recorded before advising

### Grade Entry
✓ Enter grades promptly after assessment
✓ Use consistent semester naming (e.g., "2024-1", "2024-2")
✓ Verify grade accuracy before saving
✓ Document grade entry dates for auditing

### Monitoring System Health
✓ Check Reports monthly for system trends
✓ Monitor recommendation acceptance rates
✓ Track prerequisite compliance issues
✓ Identify students with low course coverage

## Troubleshooting

### User Cannot Delete Himself
This is intentional for safety. Have another admin delete the account if needed.

### Course Deletion Fails
Courses with registrations or prerequisites may have constraints. Delete related data first.

### Missing Recommendations for Student
Students may not have recommendations if:
- They haven't updated their academic profile
- Their transcript data is incomplete
- Prerequisite requirements aren't met

## Admin Capabilities Summary

| Task | Supported | Notes |
|------|-----------|-------|
| Create users | ✓ | Staff accounts only; students self-register |
| Edit users | ✓ | Change name, email, role, department, level |
| Delete users | ✓ | Cascades to all related data |
| Search users | ✓ | By name, email, or role |
| Create courses | ✓ | Full course details and metadata |
| Edit courses | ✓ | All course information |
| Delete courses | ✓ | Cascades to prerequisites and records |
| Search courses | ✓ | By code, title, or department |
| Manage prerequisites | ✓ | Add/remove course requirements |
| Record grades | ✓ | Enter student academic records |
| View transcripts | ✓ | See student grade history |
| View reports | ✓ | System-wide analytics and KPIs |

## Additional Resources

- [Database Schema](../../database/schema.sql)
- [System README](../../README.md)
- [Developer Guide](../../docs/)
