# Human2Human: AI Oral Exams, Role Plays & Peer Dialogue

Add AI-facilitated oral exams, role plays, debates and guided reflections to any
Moodle course. Every learner gets a real conversation and rubric-based feedback
the moment it ends, with scores in the gradebook and embedded cohort insights.

## Description

Moodle makes it easy to deliver content at scale and to assess learners with
quizzes, assignments and forum posts. But much of that gradable work can now be
completed by AI in seconds, and none of it shows how a learner reasons in the
moment.

[Human2Human](https://human2human.ai) adds what's missing: active learning and peer learning, inside
your Moodle course. Learners take part in real conversations, such as an oral
exam, a role play, a debate with classmates or a guided reflection. An AI
facilitator probes their reasoning, asks follow-up questions and adapts to every
answer. A live conversation is far harder to fake than a written submission, and
it gives learners practice that a quiz never could.

An educator designs each activity once: the scenario, the questions worth
asking, and the rubric that defines good reasoning. Every learner then gets
their own conversation, personalized rubric-aligned feedback the moment it
ends, and a score in the Moodle gradebook. You get not only the scores, but also
a view of what the whole cohort understood.

## Key features

### Conversations, not submissions

Role plays, scenario simulations, oral exams, oral defenses, interview practice
and guided reflection, plus debates, case discussions, brainstorming and group
problem solving. Every activity is structured, with an objective, a facilitation
flow and a rubric. It is not an open-ended chatbot.

### Two modes: individual and small group

- **REFLECT**: one learner, one AI facilitator. A safe space to think out loud,
  admit confusion and try again. Learners can repeat it, so it works as
  deliberate practice, not only as a one-time assessment.
- **CONNECT**: live peer dialogue in small groups, with voice and video.
  Learners book a time slot, and the AI facilitator keeps the group focused,
  invites quieter participants in and guides the discussion toward the learning
  objective.

### Designed by educators, facilitated for every learner

The Activity & Rubric Designer and a template library help educators turn a
learning objective into a complete activity. The expertise stays human. The
result is a facilitator that runs the activity for 30 learners or 3,000, with no
extra facilitation hours.

### Immediate, rubric-aligned feedback

Every learner, not only the ones an educator had time for, receives feedback
tied to what they actually said: what they did well and one concrete next step.
Scoring is consistent across the cohort and goes straight to the Moodle
gradebook.

### See the learning process, not just the score

Full transcripts for every conversation, cohort-level analytics, and Learning
Pulse semantic analysis show where a group's reasoning is strong and where it
breaks down.

### Assessment that holds up in the age of AI

A written reflection can be generated in seconds. A live conversation that
adapts to each answer asks learners to reason in the moment, which makes faking
much harder.

### Native to your Moodle course

Add [Human2Human](https://human2human.ai) from the activity chooser like any other activity. Learners
find it fully embedded or launch it from the course page with no separate
login, and results flow back to Moodle.

### Accessible and multilingual

Targets WCAG 2.2 AA. All primary flows work by keyboard, and text mode is always
available as a full alternative to voice. Interface in English and Spanish; the
AI facilitator can converse in additional languages.

## Requirements

- Moodle 4.5 to 5.3.
- The External tool activity (`mod_lti`), which is part of standard Moodle.
- A [Human2Human](https://human2human.ai) account. The free plan is enough to pair and use the
  plugin.

## Getting started

1. Create a free account at [app.human2human.ai](https://app.human2human.ai).
   No credit card needed.
2. Install the plugin. In Moodle, go to **Site administration > Plugins >
   Install plugins**, upload the ZIP and follow the prompts. No shell access and
   no Composer step are needed. Alternatively, unzip it into
   `admin/tool/human2human` and visit **Site administration > Notifications**.
3. Pair the site. Go to **Site administration > Plugins > Admin tools >
   Human2Human** and select **Pair with Human2Human**. A new tab opens on
   Human2Human, where you sign in and choose the team the site belongs to.
   Nothing is copied between the two sites, and no keys or IDs are entered by
   hand. Close that tab when it says the site is paired. Back on the Human2Human
   page, the setup finishes on its own: it activates the tool, adds it to the
   activity chooser, turns on activity selection and grade sync, launches
   activities in a new window, and sends each participant's name but not their
   email address.
4. Design an activity in [Human2Human](https://human2human.ai), or start from a template.
5. In your course, turn editing on, add a Human2Human activity and select the
   activity you designed.

## External service, data and privacy

This plugin connects your Moodle site to
[Human2Human](https://human2human.ai), an external service that hosts and
facilitates the activities. A Human2Human account is required; a free plan is
available.

### What Moodle shares

Human2Human activities run through Moodle's built-in External tool (LTI 1.3).
This plugin only sets up that connection. It stores no personal data and sends
none to Human2Human itself.

When a learner opens an activity, Moodle's External tool sends Human2Human:

- the learner's Moodle user ID and their role in the course;
- the course and the activity they opened;
- their language;
- their name and email address, only if the tool's privacy settings share
  them.

Scores and feedback come back to the Moodle gradebook through the same
External tool. Because `mod_lti` is what sends and receives all of it, Moodle's
privacy registry lists this data under the External tool (`mod_lti`) and not
under this plugin, which declares that it stores and sends nothing of its own.

### How Human2Human handles it

- Conversations are processed by third-party large language model providers to
  run the activity. They are not used to train foundation models, ours or
  anyone else's.
- Data is stored in AWS US East.
- Data is encrypted in transit and at rest, with role-based access and tenant
  separation.
- Data is kept for five years by default.

## Pricing

This plugin is listed as a paid plugin because it needs the Human2Human service
to work. Downloading and installing it costs nothing. Activities run on a
[Human2Human](https://human2human.ai) plan, which comes in three tiers:

- **Free**: $0, no credit card.
  - 500 facilitation minutes
  - Up to 25 learners per activity
  - REFLECT and CONNECT in voice or text
  - Rubric-aligned scoring and feedback
  - LMS integration with grade passback
  - Cohort analytics
- **Professional**: $79 USD/month.
  - 2,000 facilitation minutes per month
  - No learner cap
  - Learning Pulse semantic transcript analysis
- **Institutional**: an annual minute pool sized to your programs.
  - Invoicing and purchase orders
  - Multiple designers on one account
  - A shared activity library
  - Design assistance and a named contact

Facilitation minutes count active session time. Full details:
[human2human.ai/pricing](https://human2human.ai/pricing).

## Support

Report bugs and ask for features in this repository's
[issue tracker](https://github.com/human2human-ai/moodle-tool_human2human/issues).
To report a security problem, see [SECURITY.md](SECURITY.md).

Pull requests are welcome. They are applied to the main [Human2Human](https://human2human.ai) source
with you credited as the author, then published here, so your pull request is
closed rather than merged.

### Links

- Website: <https://human2human.ai>
- Example activities: <https://human2human.ai/examples/>
- Terms of use: <https://human2human.ai/terms/>
- Privacy policy: <https://human2human.ai/privacy>
- Accessibility: <https://human2human.ai/accessibility>
- Documentation, bug tracker and source code:
  <https://github.com/human2human-ai/moodle-tool_human2human/>

## About

Built by the team behind Edunext, which has run online learning infrastructure
since 2013. [Human2Human](https://human2human.ai) is recognized in HolonIQ's Global EdTech 1000 and
LatAm EdTech 200.

## Licence

GNU GPL v3 or later. See [LICENSE](LICENSE).
