# Analysis of the Existing Manual Course Advisory Process

> Supports **Objective 1**: *Analyze the existing manual course advisory process in universities.*
> This document records how advising is done today, where it breaks down, and how
> each weakness maps to a feature of the Course Advisory and Recommendation System (CARS).

---

## 1. The Manual Process Today

In most universities, course advising at the start of each semester follows these steps:

1. **Student obtains records** — the student collects their transcript / result slips, often as paper printouts or scattered across portal screens.
2. **Student books an advisor** — limited advisor availability means long queues during a short registration window.
3. **Manual eligibility check** — the advisor reads the transcript and mentally checks, course by course, whether prerequisites have been passed.
4. **Verbal recommendation** — the advisor suggests courses based on memory of the programme structure and the student's standing.
5. **Form filling** — the student fills a course registration form, which is signed by the advisor and submitted to the department.
6. **No feedback loop** — there is no record of whether the advice was followed or whether it led to good outcomes.

### Actors
| Actor | Role in manual process |
|-------|------------------------|
| Student | Gathers records, queues for advice, fills forms |
| Academic Advisor | Reviews transcripts, recommends courses, approves forms |
| Department/Admin | Maintains the course list and prerequisite rules (often only on paper) |

---

## 2. Problems Identified

| # | Problem | Cause | Consequence |
|---|---------|-------|-------------|
| P1 | Limited advisor access | One advisor for many students in a short window | Rushed or skipped sessions |
| P2 | Inconsistent advice | Relies on each advisor's memory | Different students get different guidance |
| P3 | Prerequisite errors | Manual, error-prone cross-checking | Students register for courses they aren't eligible for |
| P4 | Records are fragmented | Transcripts on paper / multiple screens | Slow, incomplete eligibility checks |
| P5 | No personalisation | No time to weigh each student's grades/level | Generic, one-size-fits-all advice |
| P6 | No measurement | Nothing tracks whether advice was useful | The process never improves |

---

## 3. Data Gathered During Analysis

The analysis was informed by the problem statement in [PRD.md](PRD.md) and typical
departmental advising practice. The key data the manual process *depends on* — and which
CARS therefore digitises — is:

- **Student academic records** (courses taken, grades, pass/fail status, level).
- **Course catalogue** (code, title, credit units, level, semester, core vs elective).
- **Prerequisite rules** (which course requires which).
- **Programme/level structure** used to decide what is appropriate "next".

---

## 4. How CARS Addresses Each Problem

| Problem | CARS feature | Where in the system |
|---------|--------------|---------------------|
| P1 Limited access | Self-service recommendations, available any time | `recommendations.php` + recommendation engine |
| P2 Inconsistent advice | Single rule-based engine applied uniformly | `RecommendationController::generate()` |
| P3 Prerequisite errors | Suggestions are gated on passed prerequisites | `prerequisites` table + `PrerequisiteModel` |
| P4 Fragmented records | Central academic-records store with GPA | `academic_records` table + `AcademicRecordModel` |
| P5 No personalisation | Scoring uses grades, retakes, level and programme | `RecommendationController` scoring rules |
| P6 No measurement | Acceptance & compliance metrics | `EvaluationModel` + `report.php` (Objective 5) |

---

## 5. Summary

The manual process is **slow, inconsistent, error-prone, and unmeasured**. Its core
activities — checking records, verifying prerequisites, and matching courses to a
student's level and performance — are deterministic rules that can be automated.
CARS digitises the underlying data (records, courses, prerequisites) and applies those
rules consistently and instantly, while adding the measurement loop the manual process
lacks. This analysis justifies the design choices implemented in Objectives 2–5.
