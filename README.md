# Academic Administration Intelligent Scheduler & Automation System

An autonomous academic administration assistant designed for the Academic Administration Bureau (Biro Administrasi Akademik dan Kemahasiswaan - BAAK). The system handles student course registration (KRS), automated conflict-free class scheduling, and official student enrolment certificate generation through an agentic workflow with real-time reasoning transparency.

---

## 1. Domain Scope & System Overview

Academic administrators manage high-stakes scheduling constraints, student enrollment compliance, and formal certification requests each semester. This application automates these operations via an autonomous agent pipeline:

- **Course Registration (KRS)**: Validates semester credit limits (SKS), prerequisite chains, and student academic standing before confirming course enrollments.
- **Academic Scheduling**: Computes conflict-free allocations across courses, instructors, physical rooms, and student cohorts using constraint satisfaction heuristics.
- **Student Enrolment Certificates**: Automatically generates standardized verification certificates (Surat Keterangan Mahasiswa Aktif) for enrolled students.
- **Source Ingestion & Data Privacy**: Ingests external academic datasets securely while operating strictly on anonymized, non-sensitive records.

---

## 2. Core Functional Modules (MVP)

### Course Registration Engine (KRS)

- Validates course prerequisite graphs and student eligibility.
- Enforces maximum semester credit limits (SKS) based on prior grade point index (IPS/IPK).
- Prevents concurrent enrollment in overlapping course time slots.
- Produces finalized student study plans with validation audit logs.

### Intelligent Schedule Optimizer

- Resolves multi-dimensional scheduling constraints: room capacity, equipment requirements, lecturer availability, and cohort clash prevention.
- Automatically flags scheduling collisions and proposes alternative time-room pairings.
- Outputs structured timetable matrices exportable across departments.

### Student Enrolment Certificate Generator

- Verifies real-time active student status and tuition fee clearance.
- Populates official administrative templates with verified academic metadata.
- Issues digitally verifiable certificate records ready for administrative dispatch.

### Source Data Ingestion Interface

- Interactive UI upload interface supporting CSV and JSON data sources (curriculum matrices, room inventories, lecturer availability, course catalogs).
- **Data Privacy by Design**: Operates purely on synthetic and non-sensitive identifiers. The system strictly excludes personally identifiable information (PII) such as national identity numbers (KTP), personal contact details, or financial credentials.

### Execution Telemetry & Agent Transparency

- **Real-Time Thinking Trace**: Visualizes agent reasoning steps, evaluated constraints, and tool invocation sequences directly on the user interface.
- **Task Summary Badges**: Displays execution phase status (`planning`, `executing`, `validating`, `completed`) alongside latency metrics.
- **Audit Log Panel**: Detailed breakdown of decision rationale for administrative verification.

---

## 3. Operational Workflow (Input, Agent Execution, Output)

The system operates through a deterministic three-stage lifecycle:

```
[ Input Layer ] ───► [ Autonomous Agent Core ] ───► [ Output Layer ]
- Source Datasets    - Constraint Evaluation          - Final Timetable Matrix
- Admin Prompts      - Conflict Resolution Heuristics - Approved Study Plan (KRS)
- Student Requests   - Dynamic Tool Invocations       - Enrolment Certificate
                     - Thinking & Trace Logging       - Execution Summary Log
```

### 1. Input Stage

- **Administrative Directives**: User requests via conversational input (e.g., "Generate conflict-free schedule for 3rd semester Informatics" or "Process enrolment certificate for Student ID 20231001").
- **Uploaded Data Sources**: Tabular inputs containing course lists, room capacities, time slots, and lecturer time preferences.
- **Student Parameter Payloads**: Student study plan submissions and semester credit target requests.

### 2. Agent Execution Stage

- **Parsing & Intent Classification**: The agent extracts target entities, time bounds, and administrative constraints.
- **Constraint Validation**: Evaluates capacity caps, prerequisite rules, and room availability against the ingested dataset.
- **Autonomous Reasoning & Thinking**: Computes optimal slot assignments, resolving detected schedule overlaps iteratively.
- **Telemetry Broadcasting**: Streams step-by-step execution status and internal reasoning checkpoints to the UI dashboard.

### 3. Output Stage

- **Timetable Matrices**: Complete schedule tables grouped by day, time slot, lecturer, and room allocation.
- **Approved Study Plans (KRS)**: Validated student enrollment cards with assigned course codes and total credits.
- **Enrolment Certificates**: Formatted academic standing documents containing verification metadata.
- **Execution Summary**: Performance log summarizing processed records, verified constraints, execution time, and resolution status.

---

## 4. Architecture & Data Flow

```
┌────────────────────────────────────────────────────────┐
│                      Web Client UI                     │
│  ┌───────────────────┬───────────────────────────────┐ │
│  │ Source Upload UI  │ Agent Status & Thinking Trace │ │
│  └───────────────────┴───────────────────────────────┘ │
└───────────────────────────┬────────────────────────────┘
                            │ API / IPC
┌───────────────────────────▼────────────────────────────┐
│                    Agent Core Pipeline                 │
│  ┌──────────────────────────────────────────────────┐  │
│  │ Intent Classifier & Parameter Extractor          │  │
│  └────────────────────────┬─────────────────────────┘  │
│                           ▼                            │
│  ┌──────────────────────────────────────────────────┐  │
│  │ Constraint & Heuristics Engine                   │  │
│  │ - Room Capacity Checker                          │  │
│  │ - Credit Limit (SKS) Validator                   │  │
│  │ - Time Slot Collision Resolver                   │  │
│  └────────────────────────┬─────────────────────────┘  │
│                           ▼                            │
│  ┌──────────────────────────────────────────────────┐  │
│  │ Document & Matrix Generator                      │  │
│  │ - KRS Record Builder                             │  │
│  │ - Schedule Grid Exporter                         │  │
│  │ - Certificate Builder                            │  │
│  └──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
```

---

## 5. Getting Started

### Prerequisites

- Node.js (v18.0.0 or higher) / Python (v3.10 or higher depending on runtime selected)
- Package manager: `npm`, `pnpm`, or `pip`

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/<org>/HACKATHON-Scheduler.git
   cd HACKATHON-Scheduler
   ```

2. Install dependencies:

   ```bash
   npm install
   ```

   *(or* `pip install -r requirements.txt` *if running Python backend service)*

3. Configure environment parameters:

   ```bash
   cp .env.example .env
   ```

   Provide valid configuration parameters (e.g., API keys, gateway endpoints, server port).

4. Run development server:

   ```bash
   npm run dev
   ```

5. Open application interface: Navigate to `http://localhost:3000` in your web browser.

---

## 6. Demonstration Guide

To demonstrate the full MVP flow:

1. **Upload Academic Source Data**: Navigate to the Data Source tab, upload course roster and room matrices (sample templates available in `data/samples/`).
2. **Execute Schedule Generation**: Submit scheduling parameters via the Admin prompt. Observe the real-time thinking trace panel as the agent identifies overlaps and allocates slots.
3. **Inspect Timetable Matrix**: Review the generated conflict-free grid and verify lecturer and room assignments.
4. **Process KRS & Enrolment Certificate**: Trigger student validation for a designated student ID to inspect automated SKS boundary verification and instant certificate generation.
5. **Review Execution Summary**: Confirm all processing steps in the task summary log.

---

## 7. Data Privacy & Compliance

All test datasets and operational records used by this system strictly follow privacy-safe design principles:

- No real government identifiers or national identity cards (KTP) are processed or stored.
- All student names, registration numbers, and instructor allocations are synthetic.
- File uploads are validated in-memory and sanitized against prompt injection and arbitrary file execution.
