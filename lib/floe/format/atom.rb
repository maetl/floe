require 'rubygems'
require 'builder'

module Floe
  
  module Format
    
    class Atom
      include Builder
      def self.write(entries, feed)
        doc = XmlMarkup.new(:indent=>2)
        doc.instruct!
        atom = doc.feed("xmlns"=>"http://www.w3.org/2005/Atom") do |f|
          doc.title(feed.title)
          doc.subtitle(feed.subtitle)
          doc.link("rel"=>"self", "type"=>"application/xml+atom", "href"=>feed.self)
          doc.link("rel"=>"alternate", "type"=>"text/html", "href"=>feed.alternate)
          doc.tag!(:id, feed.atom_id)
          doc.updated(feed.updated)
          doc.author { |n| n.name(feed.author.name) }
          entries.reverse_each do |entry|
            doc.entry do |e|
              doc.tag!(:id, entry.atom_id)
              doc.title(entry.title)
              doc.link("type"=>"application/zip", "href"=>entry.link)
              doc.published(entry.published)
              doc.updated(entry.updated)
              doc.summary(entry.summary)
            end
          end
        end
        atom
      end
      
    end
    
  end
  
end