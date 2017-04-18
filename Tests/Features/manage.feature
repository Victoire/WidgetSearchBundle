@mink:selenium2 @alice(Page) @alice(CharacterTemplates) @reset-schema
Feature: Manage a Search widget

    Background:
        Given I am on homepage

    @smartStep
    Scenario: I can create a new Search widget
        When I switch to "layout" mode
        Then I should see "New content"
        When I select "Search" from the "1" select of "main_content" slot
        Then I should see "Widget (Search)"
        And I should see "1" quantum
        When I check the "_a_static_widget_search[emitter]" checkbox
        And I check the "_a_static_widget_search[receiver]" checkbox
        And I select "#2 Homepage" from "_a_static_widget_search[resultsPage]"
        And I submit the widget
        Then I should see the success message for Widget edit
        When I reload the page
        And I switch to "layout" mode
        Then I should see "New content"
        When I select "Plain Text" from the "1" select of "victoire_widget_search" slot
        Then I should see "Widget (Plain Text)"
        When I fill in "_a_static_widget_text[content]" with "No results"
        And I submit the widget
        Then I should see the success message for Widget edit

    Scenario: I can search a BusinessEntity
        Given I can create a new Search widget
        When I use WidgetSearch to search for "gfdsghfdhgfhdf"
        Then I should see "No results"
        And I should not see "Jedi anakin"
        When I use WidgetSearch to search for "anakin"
        Then I should see "Jedi anakin"
        And I should not see "No results"
        When I follow "Jedi anakin"
        Then I should be on "/en/jedi-anakin"

    Scenario: I can search a Widget
        And I can create a new Search widget
        When I am on "/en/english-test"
        And I switch to "layout" mode
        Then I should see "New content"
        When I select "Title & headings" from the "1" select of "main_content" slot
        Then I should see "Widget (Title & headings)"
        And I should see "1" quantum
        When I fill in "_a_static_widget_title[content]" with "Why Star Trek is better than Star Wars ( ͡° ͜ʖ ͡°)"
        And I submit the widget
        Then I should see the success message for Widget edit
        When I reload the page
        Then I should see "Why Star Trek is better than Star Wars"
        When I am on homepage
        And I use WidgetSearch to search for "Star Wars"
        Then I should see "English test"
        And I should not see "No results"
        When I follow "English test"
        Then I should be on "/en/english-test"

    Scenario: I can search a Page
        Given I can create a new Search widget
        When I use WidgetSearch to search for "gfdsghfdhgfhdf"
        Then I should see "No results"
        And I should not see "English test"
        When I use WidgetSearch to search for "english"
        Then I should see "English test"
        And I should not see "No results"
        When I follow "English test"
        Then I should be on "/en/english-test"