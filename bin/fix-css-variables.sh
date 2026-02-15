#!/bin/bash
#
# Script to add kiyose- prefix to all CSS custom properties
# This ensures compliance with stylelint custom-property-pattern rule
#

set -e

# Define the variables that need to be renamed
# Format: "old-name:new-name"
declare -a variables=(
	"color-primary:kiyose-color-primary"
	"color-secondary:kiyose-color-secondary"
	"color-accent:kiyose-color-accent"
	"color-background:kiyose-color-background"
	"color-burgundy:kiyose-color-burgundy"
	"color-text:kiyose-color-text"
	"color-text-light:kiyose-color-text-light"
	"color-border:kiyose-color-border"
	"color-gold:kiyose-color-gold"
	"color-gold-light:kiyose-color-gold-light"
	"color-gold-transparent:kiyose-color-gold-transparent"
	"color-primary-dark:kiyose-color-primary-dark"
	"color-white:kiyose-color-white"
	"color-link:kiyose-color-link"
	"color-link-hover:kiyose-color-link-hover"
	"font-heading:kiyose-font-heading"
	"font-body:kiyose-font-body"
	"font-size-xs:kiyose-font-size-xs"
	"font-size-sm:kiyose-font-size-sm"
	"font-size-base:kiyose-font-size-base"
	"font-size-lg:kiyose-font-size-lg"
	"font-size-xl:kiyose-font-size-xl"
	"font-size-2xl:kiyose-font-size-2xl"
	"font-size-3xl:kiyose-font-size-3xl"
	"font-size-4xl:kiyose-font-size-4xl"
	"line-height-tight:kiyose-line-height-tight"
	"line-height-normal:kiyose-line-height-normal"
	"line-height-relaxed:kiyose-line-height-relaxed"
	"line-height-snug:kiyose-line-height-snug"
	"spacing-xs:kiyose-spacing-xs"
	"spacing-sm:kiyose-spacing-sm"
	"spacing-md:kiyose-spacing-md"
	"spacing-lg:kiyose-spacing-lg"
	"spacing-xl:kiyose-spacing-xl"
	"spacing-2xl:kiyose-spacing-2xl"
	"spacing-3xl:kiyose-spacing-3xl"
	"container-max-width:kiyose-container-max-width"
	"content-max-width:kiyose-content-max-width"
	"transition-fast:kiyose-transition-fast"
	"transition-base:kiyose-transition-base"
	"transition-slow:kiyose-transition-slow"
	"z-index-dropdown:kiyose-z-index-dropdown"
	"z-index-sticky:kiyose-z-index-sticky"
	"z-index-overlay:kiyose-z-index-overlay"
	"z-index-modal:kiyose-z-index-modal"
	"z-index-skip-link:kiyose-z-index-skip-link"
)

# Find all CSS files
css_files=$(find latelierkiyose -name "*.css" -type f)

echo "Starting CSS variable renaming..."
echo "Found $(echo "$css_files" | wc -l) CSS files"

# Process each variable
for var_pair in "${variables[@]}"; do
	old_var=$(echo "$var_pair" | cut -d':' -f1)
	new_var=$(echo "$var_pair" | cut -d':' -f2)
	
	echo "Renaming --${old_var} to --${new_var}..."
	
	# Replace in all CSS files
	for file in $css_files; do
		# Use sed with backup, then remove backup
		sed -i.bak "s/--${old_var}/--${new_var}/g" "$file"
		rm "${file}.bak"
	done
done

echo "Done! All CSS variables have been renamed."
echo "Running stylelint to verify..."
npm run lint:css
