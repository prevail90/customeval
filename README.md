#Custom Evaluations Moodle Plugin 🚗📊

Flexible evaluation engine for dynamic assessments where criteria may be optional or formula-dependent

#📖 Description

Custom Evaluations is a Moodle plugin that lets educators create adaptive assessments (like driving tests, skill checklists, or project evaluations) where:

    Not all criteria may apply to every evaluation

    Teachers define custom formulas to calculate scores

    Only completed/attempted criteria count toward final grades

    Flexible grading schemes adapt to real-world scenarios

Perfect for evaluations where "one-size-fdoesn't-fit-all"!

#✨ Key Features

✅ Formula-Based Grading
SUM(criteria1, criteria3)*0.8 + bonus_points - Create mathematical formulas using criteria variables

✅ Optional Criteria
Mark questions/criteria as "conditional" or "optional"

✅ Dynamic Scoring
Automatically exclude unattempted/non-applicable criteria from calculations

✅ Evaluation Templates
Save and reuse evaluation structures (e.g., "Road Test v1")

✅ Moodle Integration
Works with Moodle Gradebook, supports groups, and exports to standard formats

#🛣️ Example Use Case: Driving Test Evaluation

    Create criteria: Parallel Parking, Highway Merging, Sign Recognition...

    Set some criteria as optional (e.g., "Winter Driving" only if test occurs in snow)

    Define formula: (mandatory_criteria_avg * 0.7) + (optional_criteria_avg * 0.3)

    Evaluators only score applicable criteria → system auto-calculates final grade

#⚙️ Installation

    Clone into Moodle's mod/ directory:
    git clone https://github.com/yourusername/custom_evaluations.git evaluation

    Visit Admin > Notifications in Moodle

    Follow standard plugin installation process

#🤝 Contributing

Open to contributions! Please:

    Fork the repository

    Create a feature branch (git checkout -b feature/amazing-feature)

    Submit a Pull Request

#📜 License

GNU GPL v3.0 (Same as Moodle core)

#❓ Support

Found a bug? Open an Issue
Need help? Send me an email at otancoic@operatortraining.academy

Developed by Prevail90 with ❤️ for flexible education assessment
